@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="fade-up max-w-2xl">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-8">Pesanan Berhasil Dibuat</h1>

    <div class="border border-green-200 bg-green-50 rounded-lg p-6 mb-8">
        <div class="flex items-center gap-3 text-green-700 mb-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm uppercase tracking-[0.2em]">Terima kasih telah berbelanja!</span>
        </div>
        <p class="text-sm text-green-600">Pesanan Anda telah dibuat. Silakan selesaikan pembayaran melalui Midtrans.</p>
    </div>

    <div class="border border-neutral-200 rounded-lg p-6 mb-8">
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Detail Pesanan</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-neutral-500">Nomor Invoice</dt><dd class="font-mono font-medium">{{ $order->invoice_no }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Tanggal</dt><dd>{{ $order->created_at->format('d M Y H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Status Pesanan</dt><dd><span class="inline-flex px-2 py-0.5 rounded text-[10px] uppercase tracking-[0.1em] {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-neutral-100 text-neutral-800') }}">{{ ucfirst($order->status) }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Status Bayar</dt><dd><span class="inline-flex px-2 py-0.5 rounded text-[10px] uppercase tracking-[0.1em] {{ ($order->payment && $order->payment->payment_status === 'paid') ? 'bg-green-100 text-green-800' : (($order->payment && $order->payment->payment_status === 'pending') ? 'bg-yellow-100 text-yellow-800' : 'bg-neutral-100 text-neutral-800') }}">{{ $order->payment ? ucfirst($order->payment->payment_status) : 'pending' }}</span></dd></div>
        </dl>
    </div>

    <!-- Ringkasan Biaya + PPN -->
    <div class="border border-neutral-200 rounded-lg p-6 mb-8">
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Ringkasan Pembayaran</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-neutral-500">Subtotal Produk</dt><dd>Rp {{ number_format($order->total, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">PPN ({{ $order->tax_rate ?? 11 }}%)</dt><dd>Rp {{ number_format($order->tax_amount ?? ($order->total * 0.11), 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Ongkir</dt><dd class="text-green-700">Gratis</dd></div>
            <hr class="border-neutral-200">
            <div class="flex justify-between font-medium text-lg"><dt>Total Harus Dibayar</dt><dd>Rp {{ number_format($order->total_paid ?? ($order->total * 1.11), 0, ',', '.') }}</dd></div>
        </dl>
    </div>

    @if ($order->payment && $order->payment->payment_status === 'pending')
        <a href="{{ route('checkout.payment', $order) }}" class="block w-full border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 mb-4">Bayar Sekarang (Midtrans Snap)</a>
    @endif

    <div class="border border-neutral-200 rounded-lg p-6 mb-8">
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Item Pesanan</h2>
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
                        <td class="py-2 pr-4">{{ $item->product->name }}</td>
                        <td class="py-2 pr-4 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="py-2 pr-4 text-center">{{ $item->qty }}</td>
                        <td class="py-2 text-right">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex gap-4">
        <a href="{{ route('pelanggan.transaksi.show', $order) }}" class="flex-1 border border-neutral-900 text-neutral-900 py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-100">Detail Transaksi</a>
        <a href="{{ route('pelanggan.invoice.download', $order) }}" class="flex-1 border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Download Invoice PDF</a>
        <a href="{{ route('produk') }}" class="flex-1 border border-neutral-900 text-neutral-900 py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-100">Lanjut Belanja</a>
    </div>
</div>
@endsection