<?php

namespace Database\Seeders;


use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Department;
use App\Models\Service;
use App\Models\Payment;
use App\Models\ServiceCategory;
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
            'needed_for_calculations' => 1
        ]);
        ServiceCategory::create([
            'name' => 'Сайты с готовым дизайном',
            'type' => ServiceCategory::READY_SITE,
            'needed_for_calculations' => 1
        ]);
        ServiceCategory::create([
            'name' => 'РК',
            'type' => ServiceCategory::RK,
            'needed_for_calculations' => 1
        ]);
        ServiceCategory::create([
            'name' => 'SEO',
            'type' => ServiceCategory::SEO,
            'needed_for_calculations' => 1
        ]);
        ServiceCategory::create([
            'name' => 'Допы',
            'type' => ServiceCategory::OTHER,
        ]);

        ServiceCategory::create([
            'name' => 'Технические услуги',
        ]);

        Service::create([
            'name' => 'Инд Лендинг',
            'service_category_id' => '1',
            'price' => 10000,
            'work_days_duration' => '24 (двадцать четыре) рабочих дня',
            'description' => 'описание Инд лендинг',
            'deal_template_ids' => json_encode([
                'law_default' => 54,
                'law_complex' => 228,
                'physic_default' => 30,
                'physic_complex' => 246
            ]),
        ]);
        Service::create([
            'name' => 'Инд КС',
            'service_category_id' => 1,
            'price' => 20000,
            'work_days_duration' => '30 (тридцать) рабочих дней',
            'description' => 'описание Инд КС',
            'deal_template_ids' => json_encode([
                'law_default' => 70,
                'law_complex' => 232,
                'physic_default' => 34,
                'physic_complex' => 250
            ]),
        ]);
        Service::create([
            'name' => 'Инд Каталог',
            'service_category_id' => 1,
            'price' => 25000,
            'work_days_duration' => '30 (тридцать) рабочих дней',
            'description' => 'описание Каталог',
            'deal_template_ids' => json_encode([
                'law_default' => 70,
                'law_complex' => 232,
                'physic_default' => 34,
                'physic_complex' => 250
            ]),
        ]);
        Service::create([
            'name' => 'Инд ИМ',
            'service_category_id' => 1,
            'price' => 140000,
            'work_days_duration' => '35 (тридцать пять) рабочих дней',
            'description' => 'описание Инд ИМ',
            'deal_template_ids' => json_encode([
                'law_default' => 198,
                'law_complex' => 234,
                'physic_default' => 36,
                'physic_complex' => 254
            ]),
        ]);
        Service::create([
            'name' => 'Гот Лендинг',
            'service_category_id' => 2,
            'price' => 25000,
            'description' => 'описание Гот лендинг',
            'work_days_duration' => '8 (восемь) рабочих дней',
            'deal_template_ids' => json_encode([
                'law_default' => 46,
                'law_complex' => 220,
                'physic_default' => 22,
                'physic_complex' => 238
            ]),
        ]);
        Service::create([
            'name' => 'Гот КС',
            'service_category_id' => 2,
            'price' => 29000,
            'description' => 'описание Гот КС',
            'work_days_duration' => '8 (восемь) рабочих дней',
            'deal_template_ids' => json_encode([
                'law_default' => 46,
                'law_complex' => 220,
                'physic_default' => 22,
                'physic_complex' => 238
            ]),
        ]);
        Service::create([
            'name' => 'Гот Каталог',
            'service_category_id' => 2,
            'price' => 58000,
            'work_days_duration' => '19 (девятнадцать) рабочих дней',
            'description' => 'описание Гот Каталог',
            'deal_template_ids' => json_encode([
                'law_default' => 50,
                'law_complex' => 224,
                'physic_default' => 26,
                'physic_complex' => 242
            ]),
        ]);
        Service::create([
            'name' => 'Гот ИМ',
            'service_category_id' => 2,
            'price' => 70000,
            'work_days_duration' => '23 (девятнадцать) рабочих дней',
            'description' => 'описание Гот ИМ',
            'deal_template_ids' => json_encode([
                'law_default' => 50,
                'law_complex' => 224,
                'physic_default' => 26,
                'physic_complex' => 242
            ]),
        ]);
        Service::create([
            'name' => 'Настройка и ведение рекламы в Яндекс Директ',
            'service_category_id' => 3,
            'price' => 25000,
            'work_days_duration' => '5 (пять) рабочих дней с момента согласования списка ключевых слов и фраз',
            'description' => 'описание рк ведение и настройка',
            'deal_template_ids' => json_encode([
                'law_default' => 62,
                'law_complex' => 62,
                'physic_default' => 38,
                'physic_complex' => 38
            ]),
        ]);

        Service::create([
            'name' => 'Базовая SEO-оптимизация',
            'service_category_id' => 4,
            'price' => 20000,
            'work_days_duration' => '15 (пятнадцать) рабочих дней',
            'description' => 'описание Базовое СЕО',
            'deal_template_ids' => json_encode([
                'law_default' => 68,
                'law_complex' => 68,
                'physic_default' => 68,
                'physic_complex' => 68
            ]),
        ]);
        Service::create([
            'name' => 'Внешняя SEO-оптимизация',
            'service_category_id' => 4,
            'price' => 20000,
            'work_days_duration' => '1 (один) календарный месяц',
            'description' => 'описание Внешнее СЕО',
            'deal_template_ids' => json_encode([
                'law_default' => 68,
                'law_complex' => 68,
                'physic_default' => 44,
                'physic_complex' => 44
            ]),
        ]);
        Service::create([
            'name' => 'Внутренняя SEO-оптимизация',
            'service_category_id' => 4,
            'price' => 20000,
            'work_days_duration' => '1 (один) календарный месяц',
            'description' => 'описание Внутреннее СЕО',
            'deal_template_ids' => json_encode([
                'law_default' => 68,
                'law_complex' => 68,
                'physic_default' => 44,
                'physic_complex' => 44
            ]),
        ]);
        Service::create([
            'name' => 'Вариативность',
            'service_category_id' => 5,
            'price' => 39000,
            'work_days_duration' => '5 (пять) рабочих дней',
            'description' => 'описание Вариативная карточка товара'
        ]);
        Service::create([
            'name' => 'Калькулятор',
            'service_category_id' => 5,
            'price' => 15000,
            'work_days_duration' => '3 (пять) рабочих дней',
            'description' => 'описание Калькулятор'
        ]);

        Service::create([
            'name' => 'Пополнение бюджета',
            'service_category_id' => 6,
            'description' => 'Пополнение бюджета'
        ]);
        Service::create([
            'name' => 'Ведение РК',
            'service_category_id' => 6,
            'description' => 'Ведение РК'
        ]);
        Service::create([
            'name' => 'Допродажа',
            'service_category_id' => 6,
            'description' => 'Допродажа'
        ]);
        Service::create([
            'name' => '2й и далее платёж за настройку',
            'service_category_id' => 6,
            'description' => '2й и далее платёж за настройку'
        ]);
    }
}
