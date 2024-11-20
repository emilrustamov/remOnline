<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('prices')->insert([
            [
                'item_id' => 1, // ID товара или услуги, для которого применяется цена (замените на реальный ID)
                'item_type' => 'product', // Укажите тип, например, product или service
                'price' => 100.00, // Цена розничная
                'currency_id' => 1, // ID валюты, замените на реальный ID
                'price_type' => 'retail', // Тип цены - розничная
                'exchange_rate' => 1.00, // Курс обмена (если применимо)
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_id' => 1, // ID того же или другого товара или услуги
                'item_type' => 'product', // Укажите тип, например, product или service
                'price' => 90.00, // Цена оптовая
                'currency_id' => 1, // ID валюты, замените на реальный ID
                'price_type' => 'wholesale', // Тип цены - оптовая
                'exchange_rate' => 1.00, // Курс обмена (если применимо)
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}

