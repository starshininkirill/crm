<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        ServiceCategory::create([
            'name' => 'Сайты с индивидуальным дизайном',
            'type' => ServiceCategory::INDIVIDUAL_SITE,
        ]);
        ServiceCategory::create([
            'name' => 'Сайты с готовым дизайном',
            'type' => ServiceCategory::READY_SITE,
        ]);
        ServiceCategory::create([
            'name' => 'РК',
            'type' => ServiceCategory::RK,
        ]);
        ServiceCategory::create([
            'name' => 'SEO',
            'type' => ServiceCategory::SEO,
        ]);
        ServiceCategory::create([
            'name' => 'Допы',
            'type' => ServiceCategory::OTHER,
        ]);

        Service::create([
            'name' => 'Инд Лендинг',
            'service_category_id' => '1',
            'price' => 10000
        ]);
        Service::create([
            'name' => 'Инд Каталог',
            'service_category_id' => 1,
            'price' => 25000
        ]);
        Service::create([
            'name' => 'Инд Интернет магазин',
            'service_category_id' => 1,
            'price' => 50000
        ]);
        Service::create([
            'name' => 'Гот Лендинг',
            'service_category_id' => 2,
            'price' => 7500
        ]);
        Service::create([
            'name' => 'Гот Каталог',
            'service_category_id' => 2,
            'price' => 15000
        ]);
        Service::create([
            'name' => 'Первичная настройка + ведение',
            'service_category_id' => 3,
            'price' => 15000
        ]);
        Service::create([
            'name' => 'Ведение РК',
            'service_category_id' => 3,
            'price' => 10000
        ]);
        Service::create([
            'name' => 'Настройка РК',
            'service_category_id' => 3,
            'price' => 7500
        ]);
        Service::create([
            'name' => 'Базовое SEO',
            'service_category_id' => 4,
            'price' => 10000
        ]);
        Service::create([
            'name' => 'Вариативная карточка товара',
            'service_category_id' => 5,
            'price' => 5000
        ]);
        Service::create([
            'name' => 'Калькулятор',
            'service_category_id' => 5,
            'price' => 10000
        ]);
    }
}
 