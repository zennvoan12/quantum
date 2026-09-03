@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="fade-up max-w-6xl mx-auto px-4 py-6">
    <h1 class="mb-8 text-xl uppercase tracking-[0.25em] font-light">Checkout</h1>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded text-xs uppercase tracking-[0.15em]">
            <ul class="list-disc pl-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="border border-neutral-200 rounded-lg p-6 bg-white shadow-sm">
                <h2 class="mb-4 text-sm uppercase tracking-[0.2em] font-medium text-neutral-800">Alamat Pengiriman</h2>
                <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded" required placeholder="Jalan, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('alamat', auth()->user()->alamat) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">No. Telepon</label>
                            <input type="text" name="no_telp" value="{{ old('no_telp', auth()->user()->no_telp) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded" placeholder="08xxxxxxxxxx">
                        </div>
                        <div>
                            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Catatan Pesanan (Opsional)</label>
                            <input type="text" name="catatan" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded" placeholder="Catatan untuk kurir">
                        </div>
                    </div>

                    <h2 class="mt-8 mb-4 text-sm uppercase tracking-[0.2em] font-medium text-neutral-800">Metode Pembayaran</h2>
                    <div class="mb-6">
                        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Pilih Metode</label>
                        <select name="payment_method" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded bg-white" required>
                            <option value="midtrans">Midtrans Gateway (QRIS, VA, E-Wallet, Kartu Kredit)</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t border-neutral-200">
                        <button type="submit" class="flex-1 bg-neutral-900 text-white px-8 py-3 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 transition-colors rounded">Buat Pesanan & Bayar</button>
                        <button type="button" id="cancel-checkout" class="border border-neutral-300 px-6 py-3 text-[11px] uppercase tracking-[0.2em] hover:border-neutral-900 transition-colors rounded">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-8 lg:mt-0">
            <div class="border border-neutral-200 rounded-lg p-6 sticky top-28 bg-white shadow-sm space-y-6">
                <h2 class="text-sm uppercase tracking-[0.2em] font-medium text-neutral-800">Ringkasan Pesanan</h2>
                <div class="space-y-3 text-sm max-h-60 overflow-y-auto pr-1">
                    @foreach ($carts as $cart)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-neutral-600 truncate max-w-[180px]">{{ $cart->product->name }} (x{{ $cart->quantity }})</span>
                            <span class="font-medium">Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                <hr class="border-neutral-200">
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Subtotal Produk</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-neutral-500">
                        <span>PPN (11%)</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Pengiriman</span>
                        <span class="text-green-700 font-medium">Gratis</span>
                    </div>
                </div>
                <hr class="border-neutral-200">
                <div class="flex justify-between font-medium text-base">
                    <span>Total Pembayaran</span>
                    <span class="text-neutral-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <button type="button" id="open-estimasi-ongkir" class="w-full border border-neutral-300 py-2.5 text-[11px] uppercase tracking-[0.2em] hover:border-neutral-900 transition-colors rounded">Cek Estimasi Ongkir</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Estimasi Ongkir -->
<div id="estimasi-ongkir-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl border border-neutral-200">
        <h3 class="text-sm uppercase tracking-[0.2em] font-medium mb-3 text-neutral-900">Estimasi Pengiriman</h3>
        <p class="text-xs text-neutral-600 mb-4">Layanan Reguler Quantum Cell: <strong class="text-neutral-900">Gratis Ongkir</strong> (2-3 Hari Kerja) ke seluruh wilayah tercakup.</p>
        <button type="button" class="btn-close-modal w-full bg-neutral-900 text-white py-2 text-xs uppercase tracking-[0.2em] rounded hover:bg-neutral-800">Tutup</button>
    </div>
</div>

<!-- Modal Batal Checkout -->
<div id="cancel-checkout-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4 shadow-xl border border-neutral-200">
        <h3 class="text-sm uppercase tracking-[0.2em] font-medium mb-3 text-neutral-900">Batalkan Checkout?</h3>
        <p class="text-xs text-neutral-600 mb-4">Item di keranjang akan tetap aman. Anda bisa kembali melanjutkan belanja.</p>
        <div class="flex gap-2">
            <a href="{{ route('cart.index') }}" class="flex-1 text-center bg-neutral-900 text-white py-2 text-xs uppercase tracking-[0.2em] rounded hover:bg-neutral-800">Ya, Keluar</a>
            <button type="button" class="btn-close-modal flex-1 border border-neutral-300 py-2 text-xs uppercase tracking-[0.2em] rounded hover:border-neutral-900">Lanjutkan</button>
        </div>
    </div>
</div>
@endsection