@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<h1 class="text-xl uppercase tracking-[0.25em] font-light mb-2">Profil Saya</h1>

@if (session('status'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded text-xs uppercase tracking-[0.15em] mb-6">
        {{ session('status') }}
    </div>
@endif

<div class="flex items-center gap-4 mb-8">
    <!-- Avatar -->
    <div class="relative">
        <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" 
             alt="Foto Profil" 
             class="w-32 h-32 rounded-full object-cover border-2 border-neutral-200"
             @if(!auth()->user()->avatar) class="bg-neutral-200" @endif>
        <input type="file" name="avatar" id="avatarUpload" class="absolute bottom-2 right-2 hidden">
        <button onclick=document.getElementById('avatarUpload').click() class="text-[10px] uppercase tracking-[0.1em] bg-neutral-900 text-white py-1 px-3 rounded hidden">Ubah Foto</button>
    </div>
    
    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded" required>
    </div>

    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Email</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded" required>
        @error('email')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="border border-neutral-200 rounded-lg p-6 space-y-5 bg-white shadow-sm mb-10">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-[11px] uppercase tracking-[0.2em] mb-1 text-neutral-600">Alamat</label>
        <textarea name="alamat" rows="3" class="w-full border border-neutral-200 px-3 py-2 text-sm focus:border-neutral-900 outline-none rounded">{{ old('alamat', auth()->user()->alamat) }}</textarea>
    </div>

    <button type="submit" class="bg-neutral-900 text-white py-3 px-6 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800 rounded">
        Simpan Perubahan
    </button>
</form>

@endsection
