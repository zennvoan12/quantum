<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\OrderObserver;

class Order extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::observe(OrderObserver::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
