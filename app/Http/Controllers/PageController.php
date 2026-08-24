<?php

namespace App\Http\Controllers;

use App\Models\AprioriLog;
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
        $totalTransaksi = Order::count();
        $totalProduk = Product::count();
        $totalItem = OrderItem::count();
        $recent = Order::with('items.product')->latest()->take(5)->get();

        // Get latest Apriori log with top rules by lift
        $latestLog = AprioriLog::latest()->first();
        $topRules = $latestLog ? $latestLog->rules()->with('productA', 'productB')->orderByDesc('lift')->take(5)->get() : collect();

        return view('admin.dashboard', compact('totalTransaksi', 'totalProduk', 'totalItem', 'recent', 'topRules'));
    }

    public function produk()
    {
        $products = Product::with('category')->orderBy('name')->paginate(12);
        return view('produk', compact('products'));
    }

    public function transaksi()
    {
        $transaksis = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('transaksi', compact('transaksis'));
    }

    public function transaksiShow(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items.product', 'payment');
        return view('transaksi-show', ['transaksi' => $order]);
    }
}
