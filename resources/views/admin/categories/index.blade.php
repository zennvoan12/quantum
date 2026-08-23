@extends('layouts.app')

@section('title', 'Kelola Kategori')

@section('content')
<div class="flex items-center justify-between mb-8">
    <h1 class="text-xl uppercase tracking-[0.25em]">Kategori</h1>
    <a href="{{ route('admin.categories.create') }}" class="border border-neutral-900 px-5 py-2 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-900 hover:text-white transition-colors">+ Tambah</a>
</div>

@if (session('success'))
    <div class="mb-6 border border-green-200 bg-green-50 px-4 py-3 text-sm">{{ session('success') }}</div>
@endif

<table class="w-full text-sm">
    <thead>
        <tr class="border-b border-neutral-200 text-left text-[11px] uppercase tracking-[0.2em] text-neutral-400">
            <th class="py-3 pr-4 font-normal">Nama</th>
            <th class="py-3 pr-4 font-normal">Slug</th>
            <th class="py-3 font-normal"></th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $c)
            <tr class="border-b border-neutral-100">
                <td class="py-3 pr-4">{{ $c->name }}</td>
                <td class="py-3 pr-4 text-neutral-500">{{ $c->slug }}</td>
                <td class="py-3 text-right whitespace-nowrap">
                    <a href="{{ route('admin.categories.edit', $c) }}" class="underline underline-offset-4 text-neutral-500 hover:text-neutral-900">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="inline ml-4" onsubmit="return confirm('Hapus kategori ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 underline underline-offset-4">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3" class="py-10 text-center text-neutral-400">Belum ada kategori.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-8">{{ $categories->links() }}</div>
@endsection