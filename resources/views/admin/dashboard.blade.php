@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="fade-up">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-2">Dashboard Admin</h1>
    <p class="text-sm text-neutral-500 mb-12">Ringkasan toko Quantum Cell.</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-16">
    <div class="border border-neutral-200 rounded-lg p-6 fade-up delay-1">
        <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-2">Total Transaksi</p>
        <p class="text-3xl font-light">{{ number_format($totalTransaksi) }}</p>
    </div>
    <div class="border border-neutral-200 rounded-lg p-6 fade-up delay-2">
        <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-2">Total Produk</p>
        <p class="text-3xl font-light">{{ number_format($totalProduk) }}</p>
    </div>
    <div class="border border-neutral-200 rounded-lg p-6 fade-up delay-3">
        <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-2">Item Terjual</p>
        <p class="text-3xl font-light">{{ number_format($totalItem) }}</p>
    </div>
</div>

<!-- Quick Links -->
<div class="mb-16 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <a href="{{ route('admin.products.index') }}" class="border border-neutral-200 p-6 hover:bg-neutral-50 transition-colors group">
        <span class="block text-sm uppercase tracking-[0.15em] group-hover:underline underline-offset-4">Kelola Produk</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="border border-neutral-200 p-6 hover:bg-neutral-50 transition-colors group">
        <span class="block text-sm uppercase tracking-[0.15em] group-hover:underline underline-offset-4">Kelola Kategori</span>
    </a>
    <a href="{{ route('admin.transaksi') }}" class="border border-neutral-200 p-6 hover:bg-neutral-50 transition-colors group">
        <span class="block text-sm uppercase tracking-[0.15em] group-hover:underline underline-offset-4">Pesanan</span>
    </a>
    <a href="{{ route('admin.apriori.index') }}" class="border border-neutral-200 p-6 hover:bg-neutral-50 transition-colors group">
        <span class="block text-sm uppercase tracking-[0.15em] group-hover:underline underline-offset-4">Analisis Apriori</span>
    </a>
</div>

<!-- Recent Orders -->
<h2 class="text-sm uppercase tracking-[0.2em] mb-4">Transaksi Terbaru</h2>
<div class="border border-neutral-200 rounded-lg overflow-x-auto mb-16">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                <th class="py-3 px-4 font-normal">Invoice</th>
                <th class="py-3 px-4 font-normal">Tanggal</th>
                <th class="py-3 px-4 font-normal text-right">Total</th>
                <th class="py-3 px-4 font-normal">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recent as $o)
                <tr class="border-b border-neutral-100 hover:bg-neutral-50">
                    <td class="py-3 px-4 font-medium">
                        <a href="{{ route('admin.orders.show', $o) }}" class="underline underline-offset-4 hover:text-neutral-900">{{ $o->invoice_no }}</a>
                    </td>
                    <td class="py-3 px-4 text-neutral-500">{{ $o->created_at->format('d/m/y H:i') }}</td>
                    <td class="py-3 px-4 text-right">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                    <td class="py-3 px-4">{{ ucfirst($o->status) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-10 text-center text-neutral-400">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Top Association Rules -->
<div class="fade-up delay-4">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm uppercase tracking-[0.2em]">Aturan Asosiasi Teratas (Lift &gt; 1)</h2>
        <a href="{{ route('admin.apriori.index') }}" class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 hover:text-neutral-900 underline underline-offset-4">Lihat Semua &rarr;</a>
    </div>
    <div class="border border-neutral-200 rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                    <th class="py-3 px-4 font-normal">If (Produk A)</th>
                    <th class="py-3 px-4 font-normal">Then (Produk B)</th>
                    <th class="py-3 px-4 font-normal text-right">Support</th>
                    <th class="py-3 px-4 font-normal text-right">Confidence</th>
                    <th class="py-3 px-4 font-normal text-right">Lift</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($topRules) && $topRules->isNotEmpty())
                    @foreach ($topRules as $rule)
                        <tr class="border-b border-neutral-100">
                            <td class="py-3 px-4">{{ $rule->productA->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4">{{ $rule->productB->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4 text-right">{{ number_format($rule->support, 4) }}</td>
                            <td class="py-3 px-4 text-right">{{ number_format($rule->confidence, 4) }}</td>
                            <td class="py-3 px-4 text-right font-medium text-green-700">{{ number_format($rule->lift ?? 0, 4) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="5" class="py-10 text-center text-neutral-400">Belum ada aturan. Jalankan analisis Apriori.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection