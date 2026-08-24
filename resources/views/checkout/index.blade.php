@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="fade-up">
    <h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Checkout</h1>

    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="border border-neutral-200 rounded-lg p-6">
                <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Alamat Pengiriman</h2>
                <form method="POST" action="{{ route('checkout.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" class="w-full border border-neutral-200 px-3 py-2 text-sm" required placeholder="Jalan, Kelurahan, Kecamatan, Kota, Provinsi, Kode Pos">{{ old('alamat') }}</textarea>
                        @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <h2 class="mt-6 mb-4 text-sm uppercase tracking-[0.2em]">Metode Pembayaran</h2>
                    <div class="mb-4">
                        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Pilih Metode</label>
                        <select name="payment_method" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
                            <option value="midtrans">Midtrans (Kartu, VA, E-Wallet, QRIS)</option>
                        </select>
                        @error('payment_method') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full border border-neutral-900 bg-neutral-900 px-8 py-3 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Buat Pesanan</button>
                </form>
            </div>
        </div>

        <div class="mt-8 lg:mt-0">
            <div class="border border-neutral-200 rounded-lg p-6 sticky top-32">
                <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Ringkasan Pesanan</h2>
                <div class="space-y-2 text-sm">
                    @foreach ($carts as $cart)
                        <div class="flex justify-between">
                            <span class="text-neutral-500">{{ $cart->product->name }} x {{ $cart->quantity }}</span>
                            <span>Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <hr class="border-neutral-200">
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Subtotal Produk</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-neutral-500">
                        <span>PPN (11%)</span>
                        <span>Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Ongkir</span>
                        <span class="text-green-700">Gratis</span>
                    </div>
                    <hr class="border-neutral-200">
                    <div class="flex justify-between font-medium text-lg">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection