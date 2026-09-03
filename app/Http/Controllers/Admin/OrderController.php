<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'items.product', 'payment')->latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product', 'payment');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['paid', 'completed'], true) && $order->payment) {
            $order->payment->update([
                'payment_status' => 'paid',
                'paid_at' => $order->payment->paid_at ?? now(),
            ]);
        }

        if ($request->status === 'cancelled' && $order->payment && $order->payment->payment_status !== 'paid') {
            $order->payment->update(['payment_status' => 'failed']);
        }

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
