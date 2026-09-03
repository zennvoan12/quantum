<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\AprioriService;

class OrderObserver
{
    public function created(Order $order): void
    {
        AprioriService::invalidate();
    }

    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            AprioriService::invalidate();
        }
    }

    public function deleted(Order $order): void
    {
        AprioriService::invalidate();
    }
}
