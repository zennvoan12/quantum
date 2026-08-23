@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-6">Dashboard</h1>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500">Total Transaksi</div>
        <div class="text-3xl font-bold">{{ number_format($totalTransaksi) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500">Produk Terdaftar</div>
        <div class="text-3xl font-bold">{{ number_format($totalProduk) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500">Item Terjual</div>
        <div class="text-3xl font-bold">{{ number_format($totalItem) }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <h2 class="font-semibold px-6 pt-5 pb-3">Transaksi Terakhir</h2>
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3 text-left">Kode</th>
                <th class="px-6 py-3 text-left">Waktu</th>
                <th class="px-6 py-3 text-left">Item</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recent as $t)
                <tr class="border-t">
                    <td class="px-6 py-3">#TRX-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3">{{ $t->created_at->format('d-m-Y H:i') }}</td>
                    <td class="px-6 py-3">{{ $t->items->pluck('name')->implode(', ') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-6 py-6 text-center text-gray-500">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
