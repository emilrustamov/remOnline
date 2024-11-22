<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'sku',
        'articul',
        'images',
        'category_id',
        'stock_quantity',
        'status',
        'barcode',
        'is_serialized',
    ];

    protected $casts = [
        'images' => 'array',
        'status' => 'boolean',
        'is_serialized' => 'boolean',
    ];

   
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function warehouseStocks()
    // {
    //     return $this->hasMany(WarehouseStock::class);
    // }
}
