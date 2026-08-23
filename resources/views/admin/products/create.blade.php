@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Tambah Produk</h1>

<form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-6">
    @csrf
    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Nama Produk</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Kategori</label>
            <select name="category_id" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Harga (Rp)</label>
            <input type="number" name="price" value="{{ old('price') }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Stok</label>
            <input type="number" name="stock" value="{{ old('stock', 0) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Status</label>
            <select name="status" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Gambar</label>
        <input type="file" name="image" class="w-full border border-neutral-200 px-3 py-2 text-sm" accept="image/*">
    </div>

    <button type="submit" class="border border-neutral-900 bg-neutral-900 px-8 py-2 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Simpan Produk</button>
</form>
@endsection
