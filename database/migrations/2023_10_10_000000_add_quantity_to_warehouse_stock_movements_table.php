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
        Schema::table('warehouse_stock_movements', function (Blueprint $table) {
            $table->integer('quantity')->after('warehouse_to'); // Добавляем колонку количества после 'warehouse_to'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stock_movements', function (Blueprint $table) {
            $table->dropColumn('quantity'); // Удаляем колонку количества
        });
    }
};
