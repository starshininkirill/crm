<?php

namespace Database\Seeders\GlobalsSeeder;

use App\Models\Contracts\Tarif;
use App\Models\TimeTracking\WorkStatus;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');

        $this->createAdsTarifs();

        $this->createSeoTarifs();

    }

    protected function createAdsTarifs()
    {
        Tarif::create([
            'name' => '1 тариф - стандарт',
            'minimal_price' => 15000,
            'optimal_price' => 15000,
            'description' => 'Что входит:</n>— стандартное ведение + 6 часов консультаций (общение) в месяц',
            'type' => Tarif::TYPE_ADS,
            'order' => 1,
        ]);
        Tarif::create([
            'name' => '2 тариф - продвинутый',
            'minimal_price' => 19000,
            'optimal_price' => 21000,
            'description' => 'стандартное ведение + анализ вебвизора для повышения конверсионности сайта и выявления технических ошибок на сайте + 4-5 часов консультаций (общение) в месяц +1 час программиста на переработки/доработки выявленных ошибок на сайте;',
            'type' => Tarif::TYPE_ADS,
            'order' => 2,
        ]);
        Tarif::create([
            'name' => '3 тариф - промакс',
            'minimal_price' => 30000,
            'optimal_price' => 35000,
            'description' => '— стандартное ведение + анализ вебвизора для повышения конверсионности сайта и выявления технических ошибок на сайте + 6 часов консультаций в месяц (общение) + гипотезы + каллибри +аналитика каллибри (сквозная, колтрекинг, подмена номера), интеграция CRM системы (при наличии у клиента). ',
            'type' => Tarif::TYPE_ADS,
            'order' => 3,
        ]);
    }

    protected function createSeoTarifs()
    {
        Tarif::create([
            'name' => 'Тариф «Минимальный»',
            'minimal_price' => 17500,
            'optimal_price' => 20000,
            'description' => 'Базовая, Внутренняя 5 страниц, Внешняя 5 страниц.',
            'type' => Tarif::TYPE_SEO,
            'order' => 1,
        ]);
        Tarif::create([
            'name' => 'Тариф «Стандартный»',
            'minimal_price' => 19000,
            'optimal_price' => 21000,
            'description' => '«Минимальный» тариф 5 страниц + написание 2 статей в месяц',
            'type' => Tarif::TYPE_SEO,
            'order' => 2,
        ]);
        Tarif::create([
            'name' => 'Тариф «Профессиональный»',
            'minimal_price' => 30000,
            'optimal_price' => 35000,
            'description' => '5 страниц + ПФ (накрутка поведенческих факторов) + статьи + дополнительные страницы на внутреннее SEO ',
            'type' => Tarif::TYPE_SEO,
            'order' => 3,
        ]);
        Tarif::create([
            'name' => 'Тариф «Супер-бизнес»',
            'minimal_price' => 60000,
            'optimal_price' => 60000,
            'description' => '5 страниц + ПФ (накрутка поведенческих факторов) + статьи + дополнительные страницы на внутреннее SEO',
            'type' => Tarif::TYPE_SEO,
            'order' => 4,
        ]);
    }
}
