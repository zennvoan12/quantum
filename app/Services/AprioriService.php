<?php

namespace App\Services;

use App\Models\AprioriLog;
use App\Models\AssociationRule;
use App\Models\FrequentItemset;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AprioriService
{
    private const CACHE_KEY = 'apriori:rules:v2';
    private const CACHE_TTL = 21600; // 6 jam (detik)

    /**
     * Ambil semua rule yang ada di cache. Kalau cache kosong → recompute.
     */
    public static function getRules(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::recompute();
        });
    }

    /**
     * Ambil rule yang mengandung produk tertentu (untuk "Sering Dibeli Bersama").
     */
    public static function getRecommendationsFor(int $productId, int $limit = 4): array
    {
        $rules = self::getRules();
        $recs = [];

        foreach ($rules as $rule) {
            // Rule bisa 'A -> B' atau 'B -> A'. Ambil consequent-nya.
            if ($rule['a'] == $productId) {
                $recs[$rule['b']] = $rule; // key by consequent id, dedupe
            } elseif ($rule['b'] == $productId) {
                $recs[$rule['a']] = $rule;
            }
        }

        // Urutkan by lift tertinggi
        usort($recs, fn($x, $y) => $y['lift'] <=> $x['lift']);

        return array_slice($recs, 0, $limit);
    }

    /**
     * Hitung ulang Apriori + simpan ke DB + simpan ke cache.
     * Dipanggil saat cache expired ATAU setelah order baru (invalidation).
     */
    public static function recompute(): array
    {
        // Ambil transaksi
        $transactions = self::getTransactions();
        $totalTransactions = count($transactions);

        if ($totalTransactions < 1) {
            return [];
        }

        // Minimum support adaptif: < 10 transaksi = 5%, else 20% (atau pakai input admin nanti)
        $minSupport = $totalTransactions < 10 ? 0.05 : 0.02;
        $minConfidence = 0.5;

        // Step 1: 1-itemset
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
            return [];
        }

        // Step 2: k-itemset iterasi
        $allFrequentItemsets = $frequent1Itemsets;
        $k = 2;
        $prevFrequent = $frequent1Itemsets;

        while (!empty($prevFrequent)) {
            $candidates = self::generateCandidates($prevFrequent, $k);
            if (empty($candidates)) break;

            $frequentK = [];
            foreach ($candidates as $candidate) {
                $count = 0;
                foreach ($transactions as $transaction) {
                    if (empty(array_diff($candidate, $transaction))) {
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

        // Step 3: Generate rules
        $rules = [];
        foreach ($allFrequentItemsets as $itemset) {
            if (count($itemset['items']) < 2) continue;

            $items = $itemset['items'];
            $support = $itemset['support'];

            for ($i = 1; $i < count($items); $i++) {
                $antecedents = self::getCombinations($items, $i);
                foreach ($antecedents as $antecedent) {
                    $consequent = array_values(array_diff($items, $antecedent));
                    if (empty($consequent)) continue;

                    $antecedentKey = implode(',', $antecedent);
                    $antecedentSupport = $allFrequentItemsets[$antecedentKey]['support'] ?? null;
                    if (!$antecedentSupport) continue;

                    $confidence = $support / $antecedentSupport;
                    if ($confidence < $minConfidence) continue;

                    $consequentKey = implode(',', $consequent);
                    $consequentSupport = $allFrequentItemsets[$consequentKey]['support'] ?? null;
                    if (!$consequentSupport) continue;

                    $lift = $confidence / $consequentSupport;
                    if ($lift <= AssociationRule::MIN_LIFT) {
                        continue;
                    }

                    // Map nama produk ke ID
                    $antecedentIds = Product::whereIn('name', $antecedent)->pluck('id', 'name');
                    $consequentIds = Product::whereIn('name', $consequent)->pluck('id', 'name');

                    foreach ($antecedentIds as $aId) {
                        foreach ($consequentIds as $cId) {
                            $rules[] = [
                                'a' => $aId,
                                'b' => $cId,
                                'a_name' => $antecedent[0],
                                'b_name' => $consequent[0],
                                'support' => round($support, 4),
                                'confidence' => round($confidence, 4),
                                'lift' => round($lift, 4),
                            ];
                        }
                    }
                }
            }
        }

        // Dedupe by (a,b) → ambil lift tertinggi
        $unique = [];
        foreach ($rules as $r) {
            $key = $r['a'] . '-' . $r['b'];
            if (!isset($unique[$key]) || $unique[$key]['lift'] < $r['lift']) {
                $unique[$key] = $r;
            }
        }
        $rules = array_values($unique);

        // Simpan ke DB (log + rules)
        self::persistToDb($rules, $minSupport, $minConfidence, $totalTransactions);

        // Simpan timestamp recompute di cache terpisah
        Cache::put('apriori:last_recompute', now()->toDateTimeString(), self::CACHE_TTL);

        return $rules;
    }

    /**
     * Hapus cache → next request akan recompute.
     * Dipanggil dari Order observer.
     */
    public static function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('apriori:last_recompute');
    }

    /**
     * Kapan terakhir dihitung (untuk badge "X menit lalu").
     */
    public static function lastRecompute(): ?string
    {
        return Cache::get('apriori:last_recompute');
    }

    private static function getTransactions(): array
    {
        return OrderItem::with('product')
            ->get()
            ->groupBy('order_id')
            ->map(fn($items) => $items->pluck('product.name')->toArray())
            ->toArray();
    }

    private static function generateCandidates(array $frequentItemsets, int $k): array
    {
        $items = array_keys($frequentItemsets);
        $candidates = [];

        for ($i = 0; $i < count($items); $i++) {
            for ($j = $i + 1; $j < count($items); $j++) {
                $set1 = explode(',', $items[$i]);
                $set2 = explode(',', $items[$j]);

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

    private static function getCombinations(array $items, int $size): array
    {
        if ($size === 1) {
            return array_map(fn($item) => [$item], $items);
        }

        $result = [];
        self::combine($items, $size, 0, [], $result);
        return $result;
    }

    private static function combine(array $items, int $size, int $start, array $current, array &$result): void
    {
        if (count($current) === $size) {
            $result[] = $current;
            return;
        }
        for ($i = $start; $i < count($items); $i++) {
            self::combine($items, $size, $i + 1, array_merge($current, [$items[$i]]), $result);
        }
    }

    private static function persistToDb(array $rules, float $minSupport, float $minConfidence, int $totalTx): void
    {
        DB::beginTransaction();
        try {
            $log = AprioriLog::create([
                'run_at' => now(),
                'min_support' => $minSupport,
                'min_confidence' => $minConfidence,
                'total_rules' => count($rules),
            ]);

            foreach ($rules as $r) {
                AssociationRule::create([
                    'apriori_log_id' => $log->id,
                    'product_id_a' => $r['a'],
                    'product_id_b' => $r['b'],
                    'support' => $r['support'],
                    'confidence' => $r['confidence'],
                    'lift' => $r['lift'],
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }
    }
}
