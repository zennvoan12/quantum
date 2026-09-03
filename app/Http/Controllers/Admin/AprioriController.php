<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssociationRule;
use App\Models\AprioriLog;
use App\Models\FrequentItemset;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AprioriController extends Controller
{
    public function index()
    {
        $logs = AprioriLog::with('rules.productA', 'rules.productB')->latest()->paginate(10);
        return view('admin.apriori.index', compact('logs'));
    }

    public function show(AprioriLog $log)
    {
        $log->load([
            'rules' => fn($q) => $q->strong()->with('productA', 'productB')->orderByDesc('lift'),
            'itemsets',
        ]);

        $totalTransactions = OrderItem::distinct('order_id')->count('order_id');

        return view('admin.apriori.show', compact('log', 'totalTransactions'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'min_support' => 'required|numeric|between:0.01,1',
            'min_confidence' => 'required|numeric|between:0.01,1',
        ]);

        $minSupport = (float) $request->min_support;
        $minConfidence = (float) $request->min_confidence;

        // Ambil data transaksi dari order_items
        $transactions = $this->getTransactions();
        $totalTransactions = count($transactions);
        
        if (empty($transactions)) {
            return back()->withErrors('Tidak ada data transaksi untuk diproses.');
        }

        // 0. Minimum Support Adaptif
        if ($totalTransactions < 10) {
            $minSupport = 0.05; 
        } else {
            $minSupport = (float) $request->min_support;
        }
        $minConfidence = (float) $request->min_confidence;

        // 1. Generate 1-itemsets
        $itemCounts = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction as $item) {
                $itemCounts[$item] = ($itemCounts[$item] ?? 0) + 1;
            }
        }

        $frequent1Itemsets = [];
        foreach ($itemCounts as $item => $count) {
            $support = $count / $totalTransactions;
            if ($support >= $minSupport) {
                $frequent1Itemsets[$item] = [
                    'items' => [$item],
                    'support' => $support,
                    'count' => $count,
                ];
            }
        }

        if (empty($frequent1Itemsets)) {
            return back()->withErrors('Tidak ada itemset yang memenuhi minimum support.');
        }

        // 2. Generate k-itemsets (k >= 2)
        $allFrequentItemsets = $frequent1Itemsets;
        $k = 2;
        $prevFrequent = $frequent1Itemsets;

        while (!empty($prevFrequent)) {
            $candidates = $this->generateCandidates($prevFrequent, $k);
            if (empty($candidates)) break;

            $frequentK = [];
            foreach ($candidates as $candidate) {
                $count = 0;
                foreach ($transactions as $transaction) {
                    if ($this->isSubset($candidate, $transaction)) {
                        $count++;
                    }
                }
                $support = $count / $totalTransactions;
                if ($support >= $minSupport) {
                    $frequentK[implode(',', $candidate)] = [
                        'items' => $candidate,
                        'support' => $support,
                        'count' => $count,
                    ];
                }
            }

            if (empty($frequentK)) break;

            $allFrequentItemsets = array_merge($allFrequentItemsets, $frequentK);
            $prevFrequent = $frequentK;
            $k++;
        }

        // 3. Generate Association Rules
        $rules = [];
        foreach ($allFrequentItemsets as $itemset) {
            if (count($itemset['items']) < 2) continue;

            $items = $itemset['items'];
            $support = $itemset['support'];

            // Generate all possible antecedent -> consequent combinations
            for ($i = 1; $i < count($items); $i++) {
                $antecedents = $this->getCombinations($items, $i);
                foreach ($antecedents as $antecedent) {
                    $consequent = array_diff($items, $antecedent);
                    if (empty($consequent)) continue;

                    $antecedentKey = implode(',', $antecedent);
                    $antecedentSupport = $allFrequentItemsets[$antecedentKey]['support'] ?? null;
                    
                    if (!$antecedentSupport) continue;

                    $confidence = $support / $antecedentSupport;
                    
                    if ($confidence >= $minConfidence) {
                        // Calculate lift
                        $consequentKey = implode(',', $consequent);
                        $consequentSupport = $allFrequentItemsets[$consequentKey]['support'] ?? null;
                        
                        if (!$consequentSupport) continue;

                        $lift = $confidence / $consequentSupport;
                        if ($lift <= AssociationRule::MIN_LIFT) {
                            continue;
                        }

                        $rules[] = [
                            'antecedent' => $antecedent,
                            'consequent' => $consequent,
                            'support' => $support,
                            'confidence' => $confidence,
                            'lift' => $lift,
                        ];
                    }
                }
            }
        }

        // 4. Save to database
        DB::beginTransaction();
        try {
            // Create log
            $log = AprioriLog::create([
                'run_at' => now(),
                'min_support' => $minSupport,
                'min_confidence' => $minConfidence,
                'total_rules' => count($rules),
            ]);

            // Save frequent itemsets
            foreach ($allFrequentItemsets as $itemset) {
                FrequentItemset::create([
                    'apriori_log_id' => $log->id,
                    'items' => json_encode($itemset['items']),
                    'support' => $itemset['support'],
                ]);
            }

            // Clear old association rules (keep only latest)
            AssociationRule::where('apriori_log_id', '!=', $log->id)->delete();

            // Save association rules
            foreach ($rules as $rule) {
                // We need to map product names to product IDs
                $antecedentProducts = Product::whereIn('name', $rule['antecedent'])->pluck('id', 'name');
                $consequentProducts = Product::whereIn('name', $rule['consequent'])->pluck('id', 'name');

                foreach ($antecedentProducts as $antName => $antId) {
                    foreach ($consequentProducts as $conName => $conId) {
                        AssociationRule::create([
                            'apriori_log_id' => $log->id,
                            'product_id_a' => $antId,
                            'product_id_b' => $conId,
                            'support' => $rule['support'],
                            'confidence' => $rule['confidence'],
                            'lift' => $rule['lift'],
                        ]);
                    }
                }
            }

            DB::commit();

            return back()->with('success', "Proses Apriori selesai. {$log->total_rules} aturan asosiasi terbentuk.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Gagal memproses Apriori: ' . $e->getMessage());
        }
    }

    private function getTransactions(): array
    {
        return OrderItem::with('product')
            ->get()
            ->groupBy('order_id')
            ->map(function ($items) {
                return $items->pluck('product.name')->toArray();
            })
            ->toArray();
    }

    private function generateCandidates(array $frequentItemsets, int $k): array
    {
        $items = array_keys($frequentItemsets);
        $candidates = [];

        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i + 1; $j < count($items); $j++) {
                $set1 = explode(',', $items[$i]);
                $set2 = explode(',', $items[$j]);
                
                // Join step: only if first k-2 items are same
                if ($k > 2) {
                    $prefix1 = array_slice($set1, 0, $k - 2);
                    $prefix2 = array_slice($set2, 0, $k - 2);
                    if ($prefix1 !== $prefix2) continue;
                }

                $candidate = array_unique(array_merge($set1, $set2));
                sort($candidate);
                
                if (count($candidate) === $k) {
                    $candidates[] = $candidate;
                }
            }
        }

        return $candidates;
    }

    private function isSubset(array $candidate, array $transaction): bool
    {
        return empty(array_diff($candidate, $transaction));
    }

    private function getCombinations(array $items, int $size): array
    {
        if ($size === 1) {
            return array_map(fn($item) => [$item], $items);
        }

        $result = [];
        $this->combine($items, $size, 0, [], $result);
        return $result;
    }

    private function combine(array $items, int $size, int $start, array $current, array &$result): void
    {
        if (count($current) === $size) {
            $result[] = $current;
            return;
        }

        for ($i = $start; $i < count($items); $i++) {
            $this->combine($items, $size, $i + 1, array_merge($current, [$items[$i]]), $result);
        }
    }
}