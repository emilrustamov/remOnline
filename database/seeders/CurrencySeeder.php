<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        DB::table('currencies')->insert([
            [
                'currency_code' => 'TMT',
                'currency_name' => 'Turkmen Manat',
                'symbol' => 'm',
                'exchange_rate' => 19.65,
                'is_default' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'currency_code' => 'CNY',
                'currency_name' => 'Yuan',
                'symbol' => '¥',
                'exchange_rate' => 7.10, // примерный курс по отношению к манату
                'is_default' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'currency_code' => 'USD',
                'currency_name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.00, // примерный курс по отношению к манату
                'is_default' => true,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
