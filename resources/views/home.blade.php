@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section (ETQ Style) -->
<div class="mb-16 border-b border-neutral-200 pb-16 text-center fade-up">
    <span class="text-[11px] uppercase tracking-[0.3em] text-neutral-400">Wardrobe Essentials</span>
    <h1 class="mt-3 text-3xl font-light uppercase tracking-[0.2em] lg:text-5xl">Worn daily. Built to last.</h1>
    <p class="mt-4 text-sm text-neutral-500 max-w-lg mx-auto">Clean and mature, that's our way of life. Discover curated collection of premium accessories.</p>
    <div class="mt-8 flex justify-center gap-4">
        <a href="{{ route('produk') }}" class="border border-neutral-900 bg-neutral-900 px-8 py-3 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 hover:text-white transition-colors">Shop All</a>
    </div>
</div>

<!-- Most Wanted / Featured Products -->
<div class="mb-12 flex items-center justify-between fade-up delay-1">
    <h2 class="text-xl uppercase tracking-[0.25em]">Most Wanted</h2>
    <a href="{{ route('produk') }}" class="text-[11px] uppercase tracking-[0.2em] text-neutral-400 hover:text-neutral-900 underline underline-offset-4">Lihat Semua &rarr;</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    @foreach ($products as $p)
                <a href="{{ route('product.show', $p) }}" class="group flex flex-col fade-up delay-2" style="animation-delay: {{ ($loop->index + 2) * 100 }}ms;">
                    <div class="relative aspect-square overflow-hidden bg-neutral-100 border border-neutral-200 mb-4">
                        @if ($p->image)
                            <img src="{{ Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center text-xs text-neutral-400">No Image</div>
                        @endif
                    </div>
                    <span class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-1">{{ $p->category->name ?? 'Aksesoris' }}</span>
                    <h3 class="text-sm font-medium uppercase tracking-[0.1em] mb-2">{{ $p->name }}</h3>
                    <div class="flex items-center justify-between mt-auto">
                        <span class="text-sm">Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                        @auth
                            @if(auth()->user()->role !== 'admin')
                                <form method="POST" action="{{ route('cart.store') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $p->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="border border-neutral-900 px-4 py-1.5 text-[10px] uppercase tracking-[0.2em] hover:bg-neutral-900 hover:text-white transition-colors">+ Keranjang</button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </a>
            @endforeach
</div>

<!-- About Section -->
<div class="mt-24 border-t border-neutral-200 pt-16 lg:grid lg:grid-cols-2 lg:gap-16 items-center fade-up delay-3">
    <div>
        <span class="text-[11px] uppercase tracking-[0.3em] text-neutral-400">About Quantum Cell</span>
        <h2 class="mt-2 text-2xl uppercase tracking-[0.15em]">Quality Essentials for Mobile Lifestyle.</h2>
    </div>
    <div>
        <p class="text-sm text-neutral-500 leading-relaxed">
            Quantum Cell is a collective of perfectionists. We design quality mobile accessories and wardrobe essentials. Our style never changes. It evolves. Clean and mature, that's our way of life. It's our code.
        </p>
    </div>
</div>
@endsection