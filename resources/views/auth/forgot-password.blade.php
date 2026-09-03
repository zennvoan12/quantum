@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="fade-up max-w-md mx-auto">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-8 text-center">Lupa Password</h1>

    <div class="border border-neutral-200 rounded-lg p-8">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-800 text-sm rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-neutral-200 px-3 py-2 text-sm" placeholder="Masukkan email terdaftar">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full border border-neutral-900 bg-neutral-900 px-8 py-3 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Kirim Link Reset</button>
        </form>

        <div class="mt-6 text-center text-sm text-neutral-500">
            <a href="{{ route('login') }}" class="underline hover:text-neutral-900">Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection