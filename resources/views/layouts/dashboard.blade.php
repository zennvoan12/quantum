@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row gap-12">
    <!-- Sidebar -->
    <aside class="w-full lg:w-64 shrink-0">
        <div class="sticky top-24 space-y-8">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                    <a href="{{ route('profile.edit') }}" class="text-[10px] uppercase tracking-[0.1em] text-neutral-500 hover:text-neutral-900">Ubah Profil</a>
                </div>
            </div>
            
            <nav class="space-y-3 text-[11px] uppercase tracking-[0.2em] text-neutral-500">
                <a href="{{ route('cart.index') }}" class="block hover:text-neutral-900">Keranjang</a>
                <a href="#" class="block hover:text-neutral-900">Wishlist</a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 min-w-0">
        @yield('dashboard-content')
    </div>
</div>
@endsection
