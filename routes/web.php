<?php

use App\Http\Controllers\Admin\AprioriController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductDetailController;
use Illuminate\Support\Facades\Route;

// Public routes (Tamu / Pelanggan - ETQ Style Storefront)
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/produk', [PageController::class, 'produk'])->name('produk');
Route::get('/produk/{product:slug}', [ProductDetailController::class, 'show'])->name('product.show');

// Auth routes (Login/Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('showLogin');
    Route::post('/login', [AuthController::class, 'login'])->name('postLogin');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('showRegister');
    Route::post('/register', [AuthController::class, 'register'])->name('postRegister');
});

// Authenticated routes (Pelanggan)
Route::middleware(['auth'])->group(function () {
    Route::get('/transaksi-saya', [PageController::class, 'transaksi'])->name('pelanggan.transaksi');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Keranjang Belanja & Checkout
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Callback Midtrans (Public Webhook)
Route::post('/payment/notification', [CheckoutController::class, 'notification'])->name('checkout.notification');

// Admin-only routes (Dashboard & Management)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [PageController::class, 'adminDashboard'])->name('dashboard');
    Route::get('/apriori', [AprioriController::class, 'index'])->name('apriori');
    Route::post('/apriori/process', [AprioriController::class, 'process'])->name('apriori.process');
    Route::get('/apriori/{log}', [AprioriController::class, 'show'])->name('apriori.show');
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
});
