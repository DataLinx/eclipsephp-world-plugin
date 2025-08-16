<?php

namespace Eclipse\World\Seeders;

use Eclipse\World\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'id' => 'EUR',
                'name' => 'Euro',
                'is_active' => true,
            ],
            [
                'id' => 'USD',
                'name' => 'US Dollar',
                'is_active' => true,
            ],
            [
                'id' => 'GBP',
                'name' => 'British Pound',
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate(
                ['id' => $currency['id']],
                $currency
            );
        }
    }
}
