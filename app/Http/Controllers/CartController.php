<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product.category')->where('user_id', Auth::id())->get();
        $total = $carts->sum(fn($c) => $c->product->price * $c->quantity);
        return view('cart.index', compact('carts', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cart = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);
        $cart->quantity += $request->quantity;
        $cart->save();

        return back()->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function update(Request $request, Cart $cart)
    {
        $this->authorizeCart($cart);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cart->product;
        if ($product->stock < $request->quantity) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $cart->update(['quantity' => $request->quantity]);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function destroy(Cart $cart)
    {
        $this->authorizeCart($cart);
        $cart->delete();
        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    private function authorizeCart(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }
    }
}