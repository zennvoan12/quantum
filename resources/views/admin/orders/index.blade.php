@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="flex items-end justify-between gap-4 mb-8">
    <div>
        <h1 class="text-xl uppercase tracking-[0.25em]">Daftar Pesanan</h1>
        <p class="mt-2 text-sm text-neutral-500">Kelola status transaksi pelanggan Quantum Cell.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="text-[11px] uppercase tracking-[0.2em] text-neutral-500 underline underline-offset-4">Dashboard</a>
</div>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
@endif

@if ($orders->isEmpty())
    <div class="border border-neutral-200 px-6 py-12 text-center text-sm text-neutral-500">
        Belum ada transaksi.
    </div>
@else
<div class="overflow-x-auto border border-neutral-200 rounded-lg">
<table class="w-full text-sm">
    <thead>
        <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
            <th class="py-3 px-4 font-normal">Invoice</th>
            <th class="py-3 px-4 font-normal">Pelanggan</th>
            <th class="py-3 px-4 font-normal">Tanggal</th>
            <th class="py-3 px-4 font-normal text-right">Total</th>
            <th class="py-3 px-4 font-normal">Status</th>
            <th class="py-3 px-4 font-normal">Pembayaran</th>
            <th class="py-3 px-4 font-normal"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $o)
            <tr class="border-b border-neutral-100 last:border-0">
                <td class="py-3 px-4 font-medium">{{ $o->invoice_no }}</td>
                <td class="py-3 px-4">{{ $o->user->name ?? 'Tamu' }}</td>
                <td class="py-3 px-4 text-neutral-500">{{ $o->created_at->format('d/m/y H:i') }}</td>
                <td class="py-3 px-4 text-right">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                <td class="py-3 px-4">
                    <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider {{ 
                        $o->status === 'completed' ? 'bg-green-100 text-green-700' : 
                        ($o->status === 'cancelled' ? 'bg-red-100 text-red-700' : 
                        ($o->status === 'paid' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700')) 
                    }}">
                        {{ $o->status }}
                    </span>
                </td>
                <td class="py-3 px-4 text-neutral-500">
                    {{ $o->payment?->payment_status ?? 'pending' }}
                </td>
                <td class="py-3 px-4 text-right">
                    <a href="{{ route('admin.orders.show', $o) }}" class="underline underline-offset-4 text-neutral-500 hover:text-neutral-900">Detail</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>

<div class="mt-8">{{ $orders->links() }}</div>
@endif
@endsection
