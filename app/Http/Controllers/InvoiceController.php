<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->role === 'admin', 403);

        $order->load('items.product', 'payment', 'user');

        $pdf = Pdf::loadView('invoice.pdf', ['order' => $order])
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . $order->invoice_no . '.pdf');
    }
}