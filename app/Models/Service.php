<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = 'services';
    protected $fillable = [
        'name',
        'description',
        'sku',
        'articul',
        'images',
        'category_id',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
        'status' => 'boolean',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
