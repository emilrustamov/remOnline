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
        Schema::create('warehouse_stock_movements', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор перемещения
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Внешний ключ на товары
            $table->foreignId('warehouse_from')->nullable()->constrained('warehouses')->onDelete('set null'); // Склад-отправитель
            $table->foreignId('warehouse_to')->nullable()->constrained('warehouses')->onDelete('set null'); // Склад-получатель
            $table->text('note')->nullable(); // Примечание к перемещению
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
