<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseProductReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'supplier_id',
        'warehouse_id',
        'product_id',
        'note',
        'purchase_price',
        'quantity',
        'invoice',
    ];

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
