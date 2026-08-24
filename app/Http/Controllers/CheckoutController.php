<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    private const TAX_RATE = 11.00; // PPN 11%

    private function midtrans(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function index()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->withErrors('Keranjang kosong.');
        }

        $subtotal = $carts->sum(fn($c) => $c->product->price * $c->quantity);
        $taxAmount = round($subtotal * (self::TAX_RATE / 100), 2);
        $total = $subtotal + $taxAmount;

        foreach ($carts as $cart) {
            if ($cart->product->stock < $cart->quantity) {
                return redirect()->route('cart.index')->withErrors('Stok produk "' . $cart->product->name . '" tidak mencukupi.');
            }
        }

        return view('checkout.index', compact('carts', 'subtotal', 'taxAmount', 'total'));
    }

    public function store(Request $request)
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            return back()->withErrors('Keranjang kosong.');
        }

        $request->validate([
            'alamat' => 'required|string|max:500',
        ]);

        $subtotal = $carts->sum(fn($c) => $c->product->price * $c->quantity);
        $taxAmount = round($subtotal * (self::TAX_RATE / 100), 2);
        $total = $subtotal + $taxAmount;

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'invoice_no' => 'INV-' . strtoupper(Str::random(10)),
                'total' => $subtotal,           // subtotal produk (sebelum PPN)
                'tax_rate' => self::TAX_RATE,
                'tax_amount' => $taxAmount,
                'total_paid' => $total,         // total yang harus dibayar (sudah termasuk PPN)
                'status' => 'pending',
                'alamat' => $request->alamat,
            ]);

            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'qty' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);

                $cart->product->decrement('stock', $cart->quantity);
            }

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
            ]);

            Cart::where('user_id', Auth::id())->delete();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Gagal membuat pesanan: ' . $e->getMessage());
        }

        return redirect()->route('checkout.payment', $order);
    }

    public function payment(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status === 'paid') {
            return redirect()->route('checkout.success', $order);
        }

        $this->midtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $order->invoice_no,
                'gross_amount' => (int) $order->total_paid, // kirim total termasuk PPN
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->no_telp ?? '',
            ],
            'item_details' => $order->items->map(fn($i) => [
                'id' => (string) $i->product_id,
                'price' => (int) $i->price,
                'quantity' => (int) $i->qty,
                'name' => Str::limit($i->product->name, 50),
            ])->all(),
            'callbacks' => [
                'finish' => route('checkout.success', $order),
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        return view('checkout.payment', compact('order', 'snapToken'));
    }

    public function notification(Request $request)
    {
        $this->midtrans();

        $notif = new \Midtrans\Notification();

        $order = Order::where('invoice_no', $notif->order_id)->first();
        if (!$order) {
            return response('order not found', 404);
        }

        match ($notif->transaction_status) {
            'capture', 'settlement' => tap($order)->update(['status' => 'paid'])
                ->payment()->update(['payment_status' => 'paid', 'paid_at' => now()]),
            'deny', 'expire', 'cancel' => tap($order)->update(['status' => 'cancelled'])
                ->payment()->update(['payment_status' => 'failed']),
            default => null,
        };

        return response('ok');
    }

    public function success(Order $order)
    {
        $this->authorizeOrder($order);
        $order->load('items.product', 'payment');
        return view('checkout.success', compact('order'));
    }

    private function authorizeOrder(Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
    }
}