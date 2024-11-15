<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Option::create([
            'name' => 'contract_main_categories',
            'value' => '["1", "2", "3", "4"]'
        ]);
        Option::create([
            'name' => 'contract_secondary_categories',
            'value' => '["3", "4", "5", "6"]'
        ]);
    }
}
