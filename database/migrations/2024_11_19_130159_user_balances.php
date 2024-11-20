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
        Schema::create('user_balances', function (Blueprint $table) {
            $table->id(); // Уникальный идентификатор записи
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // Внешний ключ на клиентов
            $table->decimal('balance', 15, 2)->default(0.00); // Текущий баланс клиента
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_balances');
    }
};
