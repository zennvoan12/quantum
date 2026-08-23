<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Quantum Cell</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ETQ Amsterdam Style Animations */
        html { scroll-behavior: smooth; }
        @keyframes fade-up { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fade-up 0.8s ease-out forwards; opacity: 0; }
        .delay-1 { animation-delay: 100ms; }
        .delay-2 { animation-delay: 200ms; }
        .delay-3 { animation-delay: 300ms; }
        .delay-4 { animation-delay: 400ms; }
        .group-hover\:scale-105:hover img { transform: scale(1.05); }
        .transition-transform.duration-700 { transition: transform 700ms ease; }
        a { transition: color 200ms ease; }
        button, input, select, textarea { transition: all 200ms ease; }
        .hover\:bg-neutral-800:hover { background-color: #1f2937; }
        .hover\:text-white:hover { color: white; }
        .hover\:underline:hover { text-decoration: underline; }
        .border-neutral-900 { border-color: #18181b; }
        .bg-neutral-900 { background-color: #18181b; }
        .text-neutral-900 { color: #18181b; }
        .text-neutral-400 { color: #a1a1aa; }
        .text-neutral-500 { color: #71717a; }
        .bg-white\/90 { background-color: rgba(255, 255, 255, 0.9); }
        .backdrop-blur { backdrop-filter: blur(8px); }
    </style>
</head>
<body class="bg-white text-neutral-900 font-light antialiased">
<header class="fixed inset-x-0 top-0 z-50 border-b border-neutral-200 bg-white/90 backdrop-blur">
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 lg:px-8">
        <a href="{{ route('home') }}" class="text-sm font-medium uppercase tracking-[0.35em]">Quantum&nbsp;Cell</a>
        
        <!-- Mobile Menu Toggle -->
        <div class="block lg:hidden">
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-neutral-900">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-16 left-0 w-full bg-white border-b border-neutral-200 lg:static lg:block lg:w-auto lg:border-0">
            <div class="flex flex-col lg:flex-row items-center gap-4 lg:gap-10 p-4 lg:p-0 text-[11px] uppercase tracking-[0.2em]">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Beranda</a>
                <a href="{{ route('produk') }}" class="{{ request()->routeIs('produk') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Produk</a>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Dashboard</a>
                        <a href="{{ route('admin.transaksi') }}" class="{{ request()->routeIs('admin.*transaksi') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Transaksi</a>
                        <a href="{{ route('admin.apriori') }}" class="{{ request()->routeIs('admin.apriori') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Apriori</a>
                    @else
                        <a href="{{ route('pelanggan.transaksi') }}" class="{{ request()->routeIs('pelanggan.transaksi') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Transaksi Saya</a>
                        <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.index') ? 'text-neutral-900 underline underline-offset-8' : 'text-neutral-400 hover:text-neutral-900' }} transition-colors">Keranjang</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-neutral-400 hover:text-neutral-900 transition-colors">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('showLogin') }}" class="text-neutral-400 hover:text-neutral-900 transition-colors">Login</a>
                    <a href="{{ route('showRegister') }}" class="text-neutral-400 hover:text-neutral-900 transition-colors">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
<main class="mx-auto min-h-screen max-w-7xl px-8 pb-24 pt-32">
    @yield('content')
</main>
<footer class="border-t border-neutral-200">
    <div class="mx-auto flex h-20 max-w-7xl items-center justify-between px-8 text-[11px] uppercase tracking-[0.25em] text-neutral-400">
        <span>Quantum Cell</span>
        <span>Analisis Asosiasi — Algoritma Apriori</span>
    </div>
</footer>
</body>
</html>
