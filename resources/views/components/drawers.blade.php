<div id="toast-container" class="fixed bottom-4 left-1/2 -translate-x-1/2 z-[70] space-y-2 pointer-events-none"></div>

@php
    $drawerCarts = auth()->check() ? \App\Models\Cart::with('product')->where('user_id', auth()->id())->get() : collect();
    $drawerCount = $drawerCarts->sum('quantity');
    $drawerSubtotal = $drawerCarts->sum(fn($c) => $c->product->price * $c->quantity);
@endphp

<!-- Cart Drawer -->
<div id="cart-drawer" class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl z-[60] flex flex-col translate-x-full will-change-transform">
    <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 bg-white">
        <h2 class="text-xs uppercase tracking-[0.25em]">Keranjang <span id="drawer-count" class="ml-1 text-neutral-400">{{ $drawerCount }}</span></h2>
        <button id="close-cart-drawer" class="text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 bg-white">
        @if ($drawerCarts->isEmpty())
            <p class="text-center text-neutral-400 text-xs uppercase tracking-[0.2em] py-12">Keranjang kosong</p>
        @else
            <div id="drawer-items" class="space-y-4">
                @foreach ($drawerCarts as $c)
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                        @if ($c->product->image)
                            <img src="{{ asset('storage/' . $c->product->image) }}" alt="{{ $c->product->name }}" class="w-14 h-14 object-cover border border-neutral-200">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm truncate">{{ $c->product->name }}</p>
                            <p class="text-xs text-neutral-500 mt-1">{{ $c->quantity }} × Rp {{ number_format($c->product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="border-t border-neutral-200 p-5 space-y-3 bg-white">
        <div class="flex justify-between text-sm">
            <span class="text-neutral-500 uppercase tracking-[0.15em] text-[11px]">Subtotal</span>
            <span id="drawer-subtotal">Rp {{ number_format($drawerSubtotal, 0, ',', '.') }}</span>
        </div>
        <a href="{{ route('cart.index') }}" class="block w-full border border-neutral-900 bg-neutral-900 text-white text-center text-[11px] uppercase tracking-[0.2em] py-3 hover:bg-neutral-800">Lihat Keranjang</a>
        @unless ($drawerCarts->isEmpty())
            <a href="{{ route('checkout.index') }}" class="block w-full border border-neutral-300 text-center text-[11px] uppercase tracking-[0.2em] py-3 hover:border-neutral-900">Checkout</a>
        @endunless
    </div>
</div>

<!-- Wishlist Drawer -->
@auth
<div id="wishlist-drawer" class="fixed inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl z-[60] flex flex-col translate-x-full will-change-transform">
    <div class="flex items-center justify-between px-5 py-4 border-b border-neutral-200 bg-white">
        <h2 class="text-xs uppercase tracking-[0.25em]">Wishlist</h2>
        <button id="close-wishlist-drawer" class="text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 bg-white">
        @php $wishlists = auth()->user()->wishlists()->with('product')->get(); @endphp
        @if ($wishlists->isEmpty())
            <p class="text-center text-neutral-400 text-xs uppercase tracking-[0.2em] py-12">Wishlist kosong</p>
        @else
            <div class="space-y-4">
                @foreach ($wishlists as $w)
                    <div class="flex items-center gap-3 border-b border-neutral-100 pb-4">
                        @if ($w->product->image)
                            <img src="{{ asset('storage/' . $w->product->image) }}" alt="{{ $w->product->name }}" class="w-14 h-14 object-cover border border-neutral-200">
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm truncate">{{ $w->product->name }}</p>
                            <p class="text-xs text-neutral-500 mt-1">Rp {{ number_format($w->product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endauth
