@extends('layouts.app')

@section('title', 'Detail Pesanan #{{ $order->invoice_no }}')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-xl uppercase tracking-[0.25em]">Detail Pesanan</h1>
        <a href="{{ route('admin.orders.index') }}" class="text-[11px] uppercase tracking-[0.2em] text-blue-600 underline">Kembali</a>
    </div>

    @if (session('success'))
        <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="border border-neutral-200 rounded-lg p-6">
            <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Info Pesanan</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-neutral-500">Invoice</dt><dd class="font-medium">{{ $order->invoice_no }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Tanggal</dt><dd>{{ $order->created_at->format('d F Y, H:i') }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Pelanggan</dt><dd>{{ $order->user->name ?? 'Tamu' }} ({{ $order->user->email ?? '-' }})</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-neutral-500 shrink-0">Alamat Kirim</dt><dd class="text-right">{{ $order->alamat ?: ($order->user->alamat ?? '-') }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Total</dt><dd class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Status</dt><dd>
                    <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider {{ 
                        $order->status === 'completed' ? 'bg-green-100 text-green-700' : 
                        ($order->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') 
                    }}">
                        {{ $order->status }}
                    </span>
                </dd></div>
            </dl>
        </div>

        <div class="border border-neutral-200 rounded-lg p-6">
            <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Pembayaran</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-neutral-500">Metode</dt><dd>{{ ucwords(str_replace('_', ' ', $order->payment?->payment_method ?? '-')) }}</dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Status</dt><dd>
                    <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider {{ 
                        ($order->payment?->payment_status ?? 'pending') === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' 
                    }}">
                        {{ $order->payment?->payment_status ?? 'pending' }}
                    </span>
                </dd></div>
                <div class="flex justify-between"><dt class="text-neutral-500">Tgl Bayar</dt><dd>{{ $order->payment?->paid_at?->format('d F Y H:i') ?? '-' }}</dd></div>
            </dl>
        </div>
    </div>

    <div class="border border-neutral-200 rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm uppercase tracking-[0.2em]">Item Pesanan</h2>
            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                @csrf
                @method('PUT')
                <select name="status" onchange="this.form.submit()" class="border border-neutral-200 px-3 py-1.5 text-sm">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                    <th class="py-2 pr-4 font-normal">Produk</th>
                    <th class="py-2 pr-4 font-normal text-right">Harga</th>
                    <th class="py-2 pr-4 font-normal text-center">Qty</th>
                    <th class="py-2 font-normal text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr class="border-b border-neutral-100">
                        <td class="py-2 pr-4">{{ $item->product->name ?? 'Produk dihapus' }}</td>
                        <td class="py-2 pr-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-2 pr-4 text-center">{{ $item->qty }}</td>
                        <td class="py-2 text-right">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection