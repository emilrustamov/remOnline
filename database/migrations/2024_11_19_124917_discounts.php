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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор скидки
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // Внешний ключ на таблицу клиентов, не может быть NULL
            $table->string('code')->unique(); // Уникальный код скидки
            $table->enum('discount_type', ['percentage', 'fixed']); // Тип скидки: процентная или фиксированная
            $table->decimal('discount_value', 15, 2); // Значение скидки
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
