<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockWriteOff extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'warehouse_id', 'product_id', 'reason', 'quantity'];

    public function supplier()
    {
        return $this->belongsTo(Client::class, 'supplier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
