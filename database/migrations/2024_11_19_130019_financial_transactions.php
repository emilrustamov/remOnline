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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор финансовой операции
            $table->enum('type', ['income', 'expense']); // Тип операции: приход или расход
            $table->decimal('amount', 15, 2); // Сумма операции
            $table->foreignId('cash_register_id')->constrained('cash_registers')->onDelete('cascade'); // Внешний ключ на кассы
            $table->foreignId('category_id')->nullable()->constrained('transaction_categories')->onDelete('set null'); // Внешний ключ на категории операций
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // Внешний ключ на клиентов
            $table->foreignId('supplier_id')->nullable()->constrained('clients')->onDelete('set null'); // Внешний ключ на поставщиков
            $table->text('note')->nullable(); // Примечание к операции
            $table->date('transaction_date'); // Дата проведения операции
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
