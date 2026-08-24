@extends('layouts.app')

@section('title', 'Detail Transaksi #{{ $transaksi->invoice_no }}')

@section('content')
<div class="fade-up max-w-3xl">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-2">Detail Transaksi</h1>
    <p class="text-sm text-neutral-500 mb-8">Invoice: <span class="font-mono">{{ $transaksi->invoice_no }}</span></p>

    <!-- Status & Info -->
    <div class="grid grid-cols-2 gap-4 mb-8 border border-neutral-200 rounded-lg p-4">
        <div>
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-1">Tanggal Pesanan</p>
            <p class="font-medium">{{ $transaksi->created_at->format('d M Y H:i') }}</p>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-1">Status Pesanan</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase tracking-[0.1em]
                @if($transaksi->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($transaksi->status === 'paid') bg-green-100 text-green-800
                @elseif($transaksi->status === 'processing') bg-blue-100 text-blue-800
                @elseif($transaksi->status === 'shipped') bg-purple-100 text-purple-800
                @elseif($transaksi->status === 'completed') bg-emerald-100 text-emerald-800
                @elseif($transaksi->status === 'cancelled') bg-red-100 text-red-800
                @else bg-neutral-100 text-neutral-800 @endif">
                {{ ucfirst($transaksi->status) }}
            </span>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-1">Metode Pembayaran</p>
            <p class="font-medium">{{ $transaksi->payment ? ucwords(str_replace('_', ' ', $transaksi->payment->payment_method)) : 'Belum dibayar' }}</p>
        </div>
        <div>
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-1">Status Pembayaran</p>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase tracking-[0.1em]
                @if($transaksi->payment && $transaksi->payment->payment_status === 'paid') bg-green-100 text-green-800
                @elseif($transaksi->payment && $transaksi->payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                @elseif($transaksi->payment && $transaksi->payment->payment_status === 'failed') bg-red-100 text-red-800
                @else bg-neutral-100 text-neutral-800 @endif">
                {{ $transaksi->payment ? ucfirst($transaksi->payment->payment_status) : 'Belum ada data' }}
            </span>
        </div>
    </div>

    <!-- Alamat -->
    @if($transaksi->alamat)
    <div class="mb-8 border border-neutral-200 rounded-lg p-4 bg-neutral-50">
        <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-2">Alamat Pengiriman</p>
        <p class="text-sm text-neutral-700 whitespace-pre-line">{{ $transaksi->alamat }}</p>
    </div>
    @endif

    <!-- Item Pesanan -->
    <div class="mb-8 border border-neutral-200 rounded-lg overflow-hidden">
        <div class="bg-neutral-50 border-b border-neutral-200 px-4 py-3">
            <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400">Item Pesanan</p>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-neutral-50">
                <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                    <th class="py-2 px-4">Produk</th>
                    <th class="py-2 px-4 text-right">Harga</th>
                    <th class="py-2 px-4 text-center">Qty</th>
                    <th class="py-2 px-4 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaksi->items as $item)
                    <tr class="border-b border-neutral-100">
                        <td class="py-2 px-4">{{ $item->product->name }}</td>
                        <td class="py-2 px-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-2 px-4 text-center">{{ $item->qty }}</td>
                        <td class="py-2 px-4 text-right font-medium">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Ringkasan Biaya + PPN 11% -->
    <div class="border border-neutral-200 rounded-lg p-4 mb-8">
        <p class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 mb-4">Ringkasan Pembayaran</p>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-neutral-500">Subtotal Produk</dt><dd class="font-medium">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">PPN ({{ $transaksi->tax_rate ?? 11 }}%)</dt><dd class="font-medium">Rp {{ number_format($transaksi->tax_amount ?? ($transaksi->total * 0.11), 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Ongkir</dt><dd class="text-green-700">Gratis</dd></div>
            <hr class="border-neutral-200">
            <div class="flex justify-between text-lg font-semibold"><dt>Total Dibayar</dt><dd>Rp {{ number_format($transaksi->total_paid ?? ($transaksi->total * 1.11), 0, ',', '.') }}</dd></div>
        </dl>
    </div>

    <div class="flex gap-4 flex-wrap">
        <a href="{{ route('pelanggan.transaksi') }}" class="flex-1 border border-neutral-900 text-neutral-900 py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-100">Kembali</a>
        @if($transaksi->payment && $transaksi->payment->payment_status === 'pending')
            <a href="{{ route('checkout.payment', $transaksi) }}" class="flex-1 border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Bayar Sekarang</a>
        @endif
        <a href="{{ route('pelanggan.invoice.download', $transaksi) }}" class="flex-1 border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Download Invoice PDF</a>
    </div>
</div>
@endsection