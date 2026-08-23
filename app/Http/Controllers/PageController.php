<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class PageController extends Controller
{
    public function home()
    {
        $products = Product::with('category')->latest()->take(8)->get();
        return view('home', compact('products'));
    }

    public function adminDashboard()
    {
        return view('admin.dashboard', [
            'totalTransaksi' => Order::count(),
            'totalProduk' => Product::count(),
            'totalItem' => OrderItem::count(),
            'recent' => Order::with('items.product')->latest()->take(5)->get(),
        ]);
    }

    public function produk()
    {
        $products = Product::with('category')->orderBy('name')->paginate(12);
        return view('produk', compact('products'));
    }

    public function transaksi()
    {
        return view('transaksi', [
            'transaksis' => Order::with('items.product')->latest()->paginate(20),
        ]);
    }
}
