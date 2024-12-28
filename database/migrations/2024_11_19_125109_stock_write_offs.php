<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_stock_write_offs', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор списания
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade'); // Внешний ключ на склады
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Внешний ключ на товары
            $table->text('reason'); // Причина списания
            $table->integer('quantity'); // Количество списанного товара
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock_write_offs');
    }
};
