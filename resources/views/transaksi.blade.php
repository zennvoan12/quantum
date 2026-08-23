@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<h1 class="text-2xl font-bold mb-6">Daftar Transaksi</h1>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 uppercase text-xs text-gray-500">
            <tr>
                <th class="px-6 py-3 text-left">Kode</th>
                <th class="px-6 py-3 text-left">Waktu</th>
                <th class="px-6 py-3 text-left">Item</th>
                <th class="px-6 py-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $t)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-6 py-3">#TRX-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-6 py-3">{{ $t->created_at->format('d-m-Y H:i') }}</td>
                    <td class="px-6 py-3">{{ $t->items->pluck('name')->implode(', ') }}</td>
                    <td class="px-6 py-3 text-right">Rp {{ number_format($t->items->sum('price'), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transaksis->links() }}</div>
@endsection
