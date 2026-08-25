@extends('layouts.app')

@section('title', 'Edit Profil Pembeli')

@section('content')
<div class="fade-up">
    <div class="max-w-xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl uppercase tracking-[0.25em]">Edit Profil</h1>
            <a href="{{ route('profile.edit') }}" class="inline-block bg-neutral-900 text-white px-4 py-2 text-xs uppercase tracking-[0.2em] hover:bg-neutral-800">Edit Profil</a>
        </div>

        @if (session('status'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-2 text-xs">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <div class="space-y-4 border border-neutral-200 rounded-lg p-6">
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full border border-neutral-200 px-3 py-2 text-sm" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Email</label>
                    <input type="email" name="email" class="w-full border border-neutral-200 px-3 py-2 text-sm" value="{{ old('email', $user->email) }}" required>
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">No. Telp</label>
                    <input type="text" name="no_telp" class="w-full border border-neutral-200 px-3 py-2 text-sm" value="{{ old('no_telp', $user->no_telp) }}">
                </div>
                <div>
                    <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Alamat</label>
                    <textarea name="alamat" rows="3" class="w-full border border-neutral-200 px-3 py-2 text-sm">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
                <button type="submit" class="w-full border border-neutral-900 bg-neutral-900 px-8 py-3 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block text-neutral-500 hover:text-neutral-800 text-xs uppercase tracking-[0.2em]">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection