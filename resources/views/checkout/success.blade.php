@extends('layouts.app')

@section('title', 'Pesanan Berhasil')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Pesanan Berhasil Dibuat</h1>

<div class="max-w-2xl">
    <div class="border border-green-200 bg-green-50 rounded-lg p-6 mb-8">
        <div class="flex items-center gap-3 text-green-700 mb-4">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span class="text-sm uppercase tracking-[0.2em]">Terima kasih telah berbelanja!</span>
        </div>
        <p class="text-sm text-green-600">Pesanan Anda sedang menunggu pembayaran. Silakan lakukan transfer ke rekening toko.</p>
    </div>

    <div class="border border-neutral-200 rounded-lg p-6 mb-8">
        <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Detail Pesanan</h2>
        <dl class="space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-neutral-500">Nomor Invoice</dt><dd class="font-medium">{{ $order->invoice_no }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Tanggal</dt><dd>{{ $order->created_at->format('d M Y H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Status</dt><dd><span class="{{ $order->status === 'pending' ? 'text-yellow-700' : 'text-green-700' }}">{{ ucfirst($order->status) }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-neutral-500">Metode Bayar</dt><dd>{{ ucwords(str_replace('_', ' ', $order->payment->payment_method)) }}</dd></div>
            <div class="flex justify-between font-medium"><dt>Total</dt><dd>Rp {{ number_format($order->total, 0, ',', '.') }}</dd></div>
        </dl>
    </div>

    @if ($order->payment->payment_status === 'pending')
        <div class="border border-neutral-200 rounded-lg p-6 mb-8 bg-neutral-50">
            <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Instruksi Pembayaran</h2>
            <ol class="space-y-2 text-sm text-neutral-600">
                <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 border border-neutral-300 rounded-full flex items-center justify-center text-[10px]">1</span> Transfer ke rekening: <strong>BRI 1234-5678-9012 a.n. Quantum Cell</strong></li>
                <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 border border-neutral-300 rounded-full flex items-center justify-center text-[10px]">2</span> Jumlah: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></li>
                <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 border border-neutral-300 rounded-full flex items-center justify-center text-[10px]">3</span> Berikan kode invoice <strong>{{ $order->invoice_no }}</strong> sebagai berita transfer.</li>
                <li class="flex items-start gap-2"><span class="flex-shrink-0 w-5 h-5 border border-neutral-300 rounded-full flex items-center justify-center text-[10px]">4</span> Upload bukti transfer di halaman ini (fitur belum tersedia) atau kirim via WhatsApp admin.</li>
            </ol>
        </div>
    @endif

    <div class="border border-neutral-200 rounded-lg p-6">
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

    <div class="mt-8 flex gap-4">
        <a href="{{ route('pelanggan.transaksi') }}" class="flex-1 border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Lihat Riwayat Transaksi</a>
        <a href="{{ route('produk') }}" class="flex-1 border border-neutral-900 text-neutral-900 py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-100">Lanjut Belanja</a>
    </div>
</div>
@endsection