<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'batch_number', 'manufacture_date', 'expire_date'];

  
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

  
    public function serialNumbers()
    {
        return $this->hasMany(ProductSerialNumber::class, 'batch_id');
    }
}
