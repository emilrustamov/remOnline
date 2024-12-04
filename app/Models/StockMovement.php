<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'warehouse_from', 'warehouse_to', 'note'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_from');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_to');
    }
}
