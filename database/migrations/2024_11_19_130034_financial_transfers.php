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
        Schema::create('financial_transfers', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор перевода
            $table->foreignId('from_cash_register_id')->constrained('cash_registers')->onDelete('cascade'); // Внешний ключ на кассу-отправитель
            $table->foreignId('to_cash_register_id')->constrained('cash_registers')->onDelete('cascade'); // Внешний ключ на кассу-получатель
            $table->decimal('amount', 15, 2); // Сумма перевода
            $table->text('note')->nullable(); // Примечание к переводу
            $table->date('transfer_date'); // Дата перевода
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transfers');
    }
};
