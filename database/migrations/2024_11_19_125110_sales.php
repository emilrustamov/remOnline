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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор продажи
            $table->unsignedBigInteger('item_id'); // Идентификатор товара или услуги
            $table->enum('item_type', ['product', 'service']); // Тип элемента: товар или услуга
            $table->foreignId('price_id')->constrained('prices')->onDelete('cascade'); // Внешний ключ на таблицу цен
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // Внешний ключ на таблицу клиентов
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null'); // Внешний ключ на таблицу складов
            $table->integer('quantity'); // Количество проданного товара или услуги
            $table->decimal('sale_price', 15, 2); // Цена продажи за единицу
            $table->decimal('total_amount', 15, 2); // Общая сумма продажи (quantity * sale_price)
            $table->enum('status', ['in_progress', 'completed', 'cancelled'])->default('in_progress'); // Статус продажи
            $table->text('note')->nullable(); // Примечание к продаже
            $table->foreignId('batch_id')->nullable()->constrained('product_batches')->onDelete('set null'); // Идентификатор партии, если применимо
            $table->timestamps(); // created_at и updated_at
        
            // Индексы для улучшения производительности
            $table->index(['item_id', 'item_type']);
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
