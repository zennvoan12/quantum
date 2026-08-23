@extends('layouts.app')

@section('title', 'Kelola Produk')

@section('content')
<div class="flex items-center justify-between mb-8">
    <h1 class="text-xl uppercase tracking-[0.25em]">Produk</h1>
    <a href="{{ route('admin.products.create') }}" class="border border-neutral-900 px-5 py-2 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-900 hover:text-white transition-colors">+ Tambah</a>
</div>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<table class="w-full text-sm">
    <thead>
        <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
            <th class="py-3 pr-4 font-normal">Gambar</th>
            <th class="py-3 pr-4 font-normal">Nama</th>
            <th class="py-3 pr-4 font-normal">Kategori</th>
            <th class="py-3 pr-4 font-normal text-right">Harga</th>
            <th class="py-3 pr-4 font-normal text-right">Stok</th>
            <th class="py-3 pr-4 font-normal">Status</th>
            <th class="py-3 font-normal"></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $p)
            <tr class="border-b border-neutral-100">
                <td class="py-3 pr-4">
                    @if ($p->image)
                        <img src="{{ Storage::url($p->image) }}" alt="{{ $p->name }}" class="h-12 w-12 object-cover border border-neutral-200">
                    @else
                        <div class="h-12 w-12 bg-neutral-100"></div>
                    @endif
                </td>
                <td class="py-3 pr-4">{{ $p->name }}</td>
                <td class="py-3 pr-4 text-neutral-500">{{ $p->category->name ?? '-' }}</td>
                <td class="py-3 pr-4 text-right">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                <td class="py-3 pr-4 text-right">{{ $p->stock }}</td>
                <td class="py-3 pr-4">
                    <span class="{{ $p->status === 'aktif' ? 'text-green-700' : 'text-red-600' }}">{{ $p->status }}</span>
                </td>
                <td class="py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.products.edit', $p) }}" class="underline underline-offset-4 text-neutral-500 hover:text-neutral-900">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $p) }}" class="inline ml-4" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 underline underline-offset-4">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="py-10 text-center text-neutral-400">Belum ada produk.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-8">{{ $products->links() }}</div>
@endsection
