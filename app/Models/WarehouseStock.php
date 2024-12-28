<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseStock extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'product_id', 'quantity'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function writeOffs()
    {
        return $this->hasMany(WarehouseStockWriteOff::class, 'warehouse_id', 'warehouse_id')
            ->whereColumn('product_id', 'product_id');
    }

    public function getStockAttribute()
    {
        $receptions = $this->hasMany(WarehouseProductReceipt::class, 'warehouse_id', 'warehouse_id')
            ->whereColumn('product_id', 'product_id')
            ->sum('quantity');

        $writeOffs = $this->writeOffs()->sum('quantity');

        $transfersIn = WarehouseStockMovement::where('warehouse_to', $this->warehouse_id)
            ->where('product_id', $this->product_id)
            ->sum('quantity');

        $transfersOut = WarehouseStockMovement::where('warehouse_from', $this->warehouse_id)
            ->where('product_id', $this->product_id)
            ->sum('quantity');

        return $receptions - $writeOffs + $transfersIn - $transfersOut;
    }
}
