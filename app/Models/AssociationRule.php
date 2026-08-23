<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationRule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'support' => 'float',
        'confidence' => 'float',
    ];

    public function log()
    {
        return $this->belongsTo(AprioriLog::class, 'apriori_log_id');
    }

    public function productA()
    {
        return $this->belongsTo(Product::class, 'product_id_a');
    }

    public function productB()
    {
        return $this->belongsTo(Product::class, 'product_id_b');
    }
}
