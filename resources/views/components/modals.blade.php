<div id="quick-view-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm mx-4 rounded border border-neutral-200 shadow-lg p-6 relative">
        <button id="close-quick-view" class="absolute top-3 right-4 text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
        <img data-qv-img src="" alt="" class="w-full h-48 object-cover border border-neutral-200 mb-4">
        <h2 data-qv-name class="text-sm uppercase tracking-[0.2em] mb-2"></h2>
        <p data-qv-price class="text-lg font-light mb-4"></p>
        <div class="flex gap-2">
            <a data-qv-link href="#" class="flex-1 border border-neutral-300 text-center py-2 text-[11px] uppercase tracking-[0.2em] hover:border-neutral-900">Lihat Detail</a>
            <button data-qv-cart data-product-id="" class="flex-1 bg-neutral-900 text-white py-2 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">+ Keranjang</button>
        </div>
    </div>
</div>

<div id="estimasi-ongkir-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm mx-4 rounded border border-neutral-200 shadow-lg p-6 relative">
        <button class="btn-close-modal absolute top-3 right-4 text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
        <h2 class="text-sm uppercase tracking-[0.25em] mb-4">Estimasi Ongkir</h2>
        <div class="space-y-3 text-xs">
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500 uppercase tracking-[0.15em]">Ongkir</span>
                <span class="font-medium">Gratis</span>
            </div>
            <div class="flex justify-between border-b border-neutral-100 pb-2">
                <span class="text-neutral-500 uppercase tracking-[0.15em]">Estimasi Pengiriman</span>
                <span>2 – 3 hari kerja</span>
            </div>
            <p class="text-neutral-400 mt-2 text-[10px] uppercase tracking-[0.15em]">Pengiriman langsung dari gudang Quantum Cell</p>
        </div>
    </div>
</div>

<div id="cancel-checkout-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm mx-4 rounded border border-neutral-200 shadow-lg p-6 relative">
        <button class="btn-close-modal absolute top-3 right-4 text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
        <h2 class="text-sm uppercase tracking-[0.25em] mb-2">Batal Checkout?</h2>
        <p class="text-xs text-neutral-500 mb-6">Pesanan belum selesai. Jika Anda keluar, data checkout akan hilang.</p>
        <div class="flex gap-2">
            <a href="{{ route('home') }}" class="flex-1 border border-neutral-900 bg-neutral-900 text-white text-center py-2 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Ya, Keluar</a>
            <button class="btn-close-modal flex-1 border border-neutral-300 text-center py-2 text-[11px] uppercase tracking-[0.2em] hover:border-neutral-900">Tetap di Sini</button>
        </div>
    </div>
</div>

<div id="otp-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm mx-4 rounded border border-neutral-200 shadow-lg p-6 relative">
        <button class="btn-close-modal absolute top-3 right-4 text-neutral-400 hover:text-neutral-900 text-xl leading-none">&times;</button>
        <h2 class="text-sm uppercase tracking-[0.25em] mb-4">Verifikasi OTP</h2>
        <p class="text-xs text-neutral-500 mb-4">Masukkan kode verifikasi yang dikirim ke email Anda.</p>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="text" name="otp" placeholder="Kode OTP" class="w-full border border-neutral-200 px-3 py-2 text-sm mb-3" required>
            <input type="password" name="password" placeholder="Kata Sandi Baru" class="w-full border border-neutral-200 px-3 py-2 text-sm mb-3" required>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Kata Sandi" class="w-full border border-neutral-200 px-3 py-2 text-sm mb-3" required>
            <button type="submit" class="w-full bg-neutral-900 text-white py-3 text-[11px] uppercase tracking-[0.2em] hover:bg-neutral-800">Verifikasi & Reset</button>
        </form>
    </div>
</div>