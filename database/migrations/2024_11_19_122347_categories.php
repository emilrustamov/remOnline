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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('parent_id')->nullable(); // Вложенность категорий
            $table->enum('type', ['product', 'service', 'both'])->default('product');
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade'); // Внешний ключ для вложенных категорий
            $table->timestamps(); // created_at и updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
