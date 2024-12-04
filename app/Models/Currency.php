<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'currency_code',
        'currency_name',
        'symbol',
        'exchange_rate',
        'is_default',
        'status',
    ];
}
