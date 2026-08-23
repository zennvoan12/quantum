@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Login</h1>

    <form method="POST" action="{{ route('postLogin') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required autofocus>
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
    </form>

    <p class="text-sm mt-4">Belum punya akun? <a href="{{ route('showRegister') }}" class="text-blue-600">Daftar</a></p>
</div>
@endsection
