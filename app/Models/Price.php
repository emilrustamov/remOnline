<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'item_id',
        'item_type',
        'price',
        'currency_id',
        'price_type',
        'exchange_rate',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
