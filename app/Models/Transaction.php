<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function items()
    {
        return $this->belongsToMany(Product::class, 'transaction_items')
            ->withPivot('qty', 'price')
            ->withTimestamps();
    }
}