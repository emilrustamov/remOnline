<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashRegisterSeeder extends Seeder
{
    public function run()
    {
        DB::table('cash_registers')->insert([
            'name' => 'Наличка',
            'balance' => 0.00, // Начальный баланс кассы
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
