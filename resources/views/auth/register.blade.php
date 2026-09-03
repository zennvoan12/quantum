@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Daftar Akun Baru</h1>

    <form method="POST" action="{{ route('postRegister') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required autofocus>
            @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
            @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm mb-1">Password</label>
            <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm mb-1">No. Telp</label>
            <input type="text" name="no_telp" value="{{ old('no_telp') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Alamat</label>
            <textarea name="alamat" class="w-full border rounded px-3 py-2">{{ old('alamat') }}</textarea>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Daftar</button>
    </form>

    <p class="text-sm mt-4">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600">Login</a></p>
</div>
@endsection
