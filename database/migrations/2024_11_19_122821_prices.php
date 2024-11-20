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
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->enum('item_type', ['product', 'service']);
            $table->decimal('price', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->string('price_type')->nullable();
            $table->decimal('exchange_rate', 15, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::dropIfExists('prices');
    }
};
