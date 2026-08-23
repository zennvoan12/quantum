<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AprioriLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'run_at' => 'datetime',
        'min_support' => 'float',
        'min_confidence' => 'float',
    ];

    public function itemsets()
    {
        return $this->hasMany(FrequentItemset::class);
    }

    public function rules()
    {
        return $this->hasMany(AssociationRule::class);
    }
}
