<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FrequentItemset extends Model
{
    protected $guarded = [];

    protected $casts = [
        'support' => 'float',
    ];

    public function log()
    {
        return $this->belongsTo(AprioriLog::class, 'apriori_log_id');
    }
}
