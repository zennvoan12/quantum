@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="fade-up max-w-md mx-auto">
    <h1 class="text-xl uppercase tracking-[0.25em] mb-8 text-center">Masuk</h1>

    <div class="border border-neutral-200 rounded-lg p-8">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-800 text-sm rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('postLogin') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full border border-neutral-200 px-3 py-2 text-sm">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-[11px] uppercase tracking-[0.2em] mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-neutral-200 px-3 py-2 text-sm">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" class="rounded border-neutral-300">
                    <span>Ingat saya</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm underline hover:text-neutral-900">Lupa Password?</a>
            </div>

            <button type="submit" class="w-full border border-neutral-900 bg-neutral-900 px-8 py-3 text-white text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Masuk</button>
        </form>

        <div class="mt-6 text-center text-sm text-neutral-500">
            Belum punya akun? <a href="{{ route('showRegister') }}" class="underline hover:text-neutral-900">Daftar</a>
        </div>
    </div>
</div>
@endsection