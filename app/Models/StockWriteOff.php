<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockWriteOff extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_id', 'reason', 'quantity'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseStock()
    {
        return $this->hasOne(WarehouseStock::class, 'warehouse_id', 'warehouse_id')
            ->whereColumn('product_id', 'product_id');
    }
}
