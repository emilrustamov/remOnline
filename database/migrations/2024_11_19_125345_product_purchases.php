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
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор закупки
            $table->string('invoice')->nullable(); // Номер счета/накладной
            $table->foreignId('supplier_id')->constrained('clients')->onDelete('cascade'); // Внешний ключ на клиентов (поставщиков)
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade'); // Внешний ключ на склады
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Внешний ключ на товары
            $table->text('note')->nullable(); // Примечание к закупке
            $table->decimal('purchase_price', 15, 2); // Цена закупки за единицу
            $table->integer('quantity'); // Количество закупленного товара
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};
