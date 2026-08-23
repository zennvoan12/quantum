@extends('layouts.app')

@section('title', 'Produk')

@section('content')
<div class="fade-up">
    <h1 class="text-2xl uppercase tracking-[0.25em] mb-2">Produk</h1>
    <p class="text-sm text-neutral-500 mb-8">Koleksi lengkap aksesoris mobile Quantum Cell.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
    @foreach ($products as $p)
                <a href="{{ route('product.show', $p) }}" class="group flex flex-col fade-up delay-2" style="animation-delay: {{ ($loop->index + 2) * 80 }}ms;">
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

{{ $products->links() }}
@endsection