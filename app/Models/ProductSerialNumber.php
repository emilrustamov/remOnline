<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerialNumber extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'batch_id', 'serial_number', 'is_sold'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(ProductBatch::class);
    }
}
