@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Keranjang Belanja</h1>

@if ($carts->isEmpty())
    <div class="border border-neutral-200 rounded-lg p-12 text-center text-neutral-400">
        Keranjang kosong. <a href="{{ route('produk') }}" class="text-blue-600 underline">Belanja sekarang</a>.
    </div>
@else
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <div class="lg:col-span-2">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
                        <th class="py-3 pr-4 font-normal">Produk</th>
                        <th class="py-3 pr-4 font-normal text-right">Harga</th>
                        <th class="py-3 pr-4 font-normal text-center">Qty</th>
                        <th class="py-3 pr-4 font-normal text-right">Subtotal</th>
                        <th class="py-3 font-normal"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($carts as $c)
                        <tr class="border-b border-neutral-100">
                            <td class="py-3 pr-4">
                                <div class="flex items-center gap-3">
                                    @if ($c->product->image)
                                        <img src="{{ Storage::url($c->product->image) }}" alt="{{ $c->product->name }}" class="h-12 w-12 object-cover border border-neutral-200">
                                    @else
                                        <div class="h-12 w-12 bg-neutral-100"></div>
                                    @endif
                                    <span>{{ $c->product->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 pr-4 text-right">Rp {{ number_format($c->product->price, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4 text-center">
                                <form method="POST" action="{{ route('cart.update', $c) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $c->quantity }}" min="1" max="{{ $c->product->stock }}" class="w-16 text-center border border-neutral-200 px-2 py-1 text-sm">
                                    <button type="submit" class="text-[11px] uppercase tracking-[0.15em] text-blue-600 hover:underline">Update</button>
                                </form>
                            </td>
                            <td class="py-3 pr-4 text-right">Rp {{ number_format($c->product->price * $c->quantity, 0, ',', '.') }}</td>
                            <td class="py-3">
                                <form method="POST" action="{{ route('cart.destroy', $c) }}" onsubmit="return confirm('Hapus dari keranjang?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 underline underline-offset-4">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8 lg:mt-0">
            <div class="border border-neutral-200 rounded-lg p-6 sticky top-32">
                <h2 class="mb-4 text-sm uppercase tracking-[0.2em]">Ringkasan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Subtotal</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-neutral-500">Ongkir</span>
                        <span class="text-green-700">Gratis</span>
                    </div>
                    <hr class="border-neutral-200">
                    <div class="flex justify-between font-medium">
                        <span>Total</span>
                        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="mt-6 block w-full border border-neutral-900 bg-neutral-900 text-white py-3 text-center text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 transition-colors">Checkout</a>
            </div>
        </div>
    </div>
@endif
@endsection