<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Переименование таблицы
        Schema::rename('product_purchases', 'warehouse_product_receipts');
    }

    public function down()
    {
        // Восстановление старого имени таблицы (если нужно откатить миграцию)
        Schema::rename('warehouse_product_receipts', 'product_purchases');
    }
};
