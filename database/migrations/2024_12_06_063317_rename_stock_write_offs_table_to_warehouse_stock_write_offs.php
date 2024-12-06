<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Переименование таблицы
        Schema::rename('stock_write_offs', 'warehouse_stock_write_offs');
    }

    public function down()
    {
        // Восстановление старого имени таблицы (если нужно откатить миграцию)
        Schema::rename('warehouse_stock_write_offs', 'stock_write_offs');
    }
};
