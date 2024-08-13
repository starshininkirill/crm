<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::create([
            'name' => 'Индивид лендинг',
            'service_category_id' => '1'
        ]);
        Service::create([
            'name' => 'Индивид каталог',
            'service_category_id' => 1,
        ]);
        Service::create([
            'name' => 'Готовый каталог',
            'service_category_id' => 2,
        ]);
        Service::create([
            'name' => 'Готовый Лендинг',
            'service_category_id' => 2,
        ]);
        Service::create([
            'name' => 'Ведение РК',
            'service_category_id' => 3,
        ]);
    }
}
 