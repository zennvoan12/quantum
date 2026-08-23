@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<h1 class="mb-8 text-xl uppercase tracking-[0.25em]">Tambah Kategori</h1>

<form method="POST" action="{{ route('admin.categories.store') }}" class="max-w-xl space-y-6">
    @csrf
    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Nama Kategori</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Slug (URL)</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="w-full border border-neutral-200 px-3 py-2 text-sm" required>
        @error('slug') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <button type="submit" class="border border-neutral-900 bg-neutral-900 px-8 py-2 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Simpan Kategori</button>
</form>
@endsection