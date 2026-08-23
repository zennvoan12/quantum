@extends('layouts.app')

@section('title', 'Hasil Apriori #{{ $log->id }}')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-xl uppercase tracking-[0.25em]">Hasil Analisis Apriori</h1>
        <a href="{{ route('admin.apriori.index') }}" class="text-[11px] uppercase tracking-[0.2em] text-blue-600 underline">Kembali</a>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <!-- Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="border border-neutral-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400">Minimum Support</p>
            <p class="text-xl font-bold">{{ $log->min_support }}</p>
        </div>
        <div class="border border-neutral-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400">Minimum Confidence</p>
            <p class="text-xl font-bold">{{ $log->min_confidence }}</p>
        </div>
        <div class="border border-neutral-200 rounded-lg p-4">
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400">Total Aturan Terbentuk</p>
            <p class="text-xl font-bold">{{ $log->total_rules }}</p>
        </div>
    </div>

    <!-- Frequent Itemsets -->
    <div class="mb-8">
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Frequent Itemsets</h2>
        <div class="border border-neutral-200 rounded-lg overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                        <th class="py-3 pr-4 font-normal">Items</th>
                        <th class="py-3 pr-4 font-normal text-right">Support</th>
                        <th class="py-3 font-normal text-right">Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($log->itemsets as $itemset)
                        <tr class="border-b border-neutral-100">
                            <td class="py-3 pr-4">{{ implode(', ', json_decode($itemset->items)) }}</td>
                            <td class="py-3 pr-4 text-right">{{ number_format($itemset->support, 4) }}</td>
                            <td class="py-3 text-right">{{ $itemset->support * $totalTransactions }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Association Rules -->
    <div>
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Aturan Asosiasi (Association Rules)</h2>
        <div class="border border-neutral-200 rounded-lg overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                        <th class="py-3 pr-4 font-normal">If (Antecedent)</th>
                        <th class="py-3 pr-4 font-normal">Then (Consequent)</th>
                        <th class="py-3 pr-4 font-normal text-right">Support</th>
                        <th class="py-3 pr-4 font-normal text-right">Confidence</th>
                        <th class="py-3 font-normal text-right">Lift</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($log->rules as $rule)
                        <tr class="border-b border-neutral-100">
                            <td class="py-3 pr-4">{{ $rule->productA->name ?? 'N/A' }}</td>
                            <td class="py-3 pr-4">{{ $rule->productB->name ?? 'N/A' }}</td>
                            <td class="py-3 pr-4 text-right">{{ number_format($rule->support, 4) }}</td>
                            <td class="py-3 pr-4 text-right">{{ number_format($rule->confidence, 4) }}</td>
                            <td class="py-3 text-right font-{{ $rule->confidence > 1 ? 'medium text-green-700' : 'normal' }}">
                                {{ number_format($rule->lift ?? 0, 4) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection