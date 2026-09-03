<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationRule extends Model
{
    /** Lift > 1 menandakan asosiasi positif (sesuai tinjauan pustaka). */
    public const MIN_LIFT = 1.0;

    protected $guarded = [];

    protected $casts = [
        'support' => 'float',
        'confidence' => 'float',
        'lift' => 'float',
    ];

    public function scopeStrong($query)
    {
        return $query->where('lift', '>', self::MIN_LIFT);
    }

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
