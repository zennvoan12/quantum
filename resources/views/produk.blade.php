@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="fade-up">
    <h1 class="text-2xl uppercase tracking-[0.25em] mb-2">Produk</h1>
    <p class="text-sm text-neutral-500 mb-8">Koleksi lengkap aksesoris mobile Quantum Cell.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    @foreach ($products as $p)
        <div class="group flex flex-col fade-up delay-2" style="animation-delay: {{ ($loop->index + 2) * 80 }}ms;">
            <div class="relative aspect-square overflow-hidden bg-neutral-100 border border-neutral-200 mb-4">
                @if ($p->image)
                    <img src="{{ Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                @else
                    <div class="flex h-full items-center justify-center text-xs text-neutral-400">No Image</div>
                @endif
                <button
                    data-quickview="{{ $p->id }}"
                    data-name="{{ $p->name }}"
                    data-price="{{ $p->price }}"
                    data-img="{{ $p->image ? Storage::url($p->image) : '' }}"
                    data-slug="{{ $p->slug ?? $p->id }}"
                    class="absolute bottom-2 right-2 bg-white/90 backdrop-blur border border-neutral-200 px-2 py-1 text-[9px] uppercase tracking-[0.15em] text-neutral-700 hover:bg-neutral-900 hover:text-white opacity-0 group-hover:opacity-100 transition-opacity">
                    Lihat
                </button>
            </div>
            <span class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-1">{{ $p->category->name ?? 'Aksesoris' }}</span>
            <h3 class="text-sm font-medium uppercase tracking-[0.1em] mb-2">{{ $p->name }}</h3>
            <div class="flex items-center justify-between mt-auto">
                <span class="text-sm">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                @auth
                    @if(auth()->user()->role !== 'admin')
                        <div class="flex items-center gap-2">
                            <button
                                data-wishlist-toggle="{{ $p->id }}"
                                class="text-neutral-400 hover:text-red-500 transition-colors {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $p->id)->exists() ? 'text-red-500' : '' }}"
                                title="Wishlist">
                                <svg class="w-4 h-4 {{ auth()->check() && auth()->user()->wishlists()->where('product_id', $p->id)->exists() ? 'fill-red-500 text-red-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>
                            <form method="POST" action="{{ route('cart.store') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $p->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="border border-neutral-900 px-4 py-1.5 text-[10px] uppercase tracking-[0.2em] hover:bg-neutral-900 hover:text-white transition-colors">+ Keranjang</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    @endforeach
</div>

{{ $products->links() }}
@endsection