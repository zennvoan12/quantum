@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="fade-up">
    <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
        <!-- Image -->
        <div class="aspect-square overflow-hidden bg-neutral-100 border border-neutral-200">
            @if ($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
            @else
                <div class="flex h-full items-center justify-center text-xs text-neutral-400">No Image</div>
            @endif
        </div>

        <!-- Details -->
        <div class="mt-8 lg:mt-0 flex flex-col h-full">
            <span class="text-[11px] uppercase tracking-[0.3em] text-neutral-400 mb-2">{{ $product->category->name ?? 'Aksesoris' }}</span>
            <h1 class="text-3xl uppercase tracking-[0.15em] mb-6">{{ $product->name }}</h1>
            <p class="text-2xl font-light mb-8">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            
            <div class="prose prose-sm text-neutral-500 leading-relaxed mb-12">
                <p>{{ $product->description ?? 'Tidak ada deskripsi produk.' }}</p>
            </div>

            @auth
                @if(auth()->user()->role !== 'admin')
                    <form method="POST" action="{{ route('cart.store') }}" class="mt-auto space-y-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center gap-4">
                            <label class="text-[11px] uppercase tracking-[0.2em]">Jumlah:</label>
                            <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 border border-neutral-200 px-3 py-2 text-sm text-center">
                            <span class="text-[10px] text-neutral-400">Stok: {{ $product->stock }}</span>
                        </div>
                        <button type="submit" class="w-full border border-neutral-900 bg-neutral-900 text-white py-4 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 transition-colors">Tambah ke Keranjang</button>
                    </form>
                @endif
            @else
                <div class="mt-auto p-4 border border-neutral-100 bg-neutral-50 text-center">
                    <p class="text-xs text-neutral-500 uppercase tracking-[0.1em]">Silakan <a href="{{ route('login') }}" class="underline">Login</a> untuk membeli.</p>
                </div>
            @endauth
        </div>
    </div>

    <!-- Recommendations -->
    <div class="mt-24 pt-16 border-t border-neutral-200">
        <h2 class="text-xl uppercase tracking-[0.25em] mb-12">Rekomendasi Produk</h2>
        @if(isset($topRules) && $topRules->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($topRules as $rule)
                    @if($rule->confidence > 0.5)
                        <a href="{{ route('product.show', $rule->productB) }}" class="group flex flex-col">
                            <div class="relative aspect-square overflow-hidden bg-neutral-100 border border-neutral-200 mb-4">
                                @if($rule->productB->image)
                                    <img src="{{ Storage::url($rule->productB->image) }}" alt="{{ $rule->productB->name }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @endif
                            </div>
                            <span class="text-[10px] uppercase tracking-[0.2em] text-neutral-400 mb-1">{{ $rule->productB->category->name ?? 'Aksesoris' }}</span>
                            <h3 class="text-sm font-medium uppercase tracking-[0.1em]">{{ $rule->productB->name }}</h3>
                            <p class="text-sm mt-2">Rp {{ number_format($rule->productB->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-neutral-500 uppercase tracking-[0.05em] mt-1">Sering dibeli bersama {{ $rule->productA->name }}</p>
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <p class="text-center text-neutral-500 italic py-12">Belum ada rekomendasi analisis Apriori.</p>
        @endif
    </div>
</div>
@endsection