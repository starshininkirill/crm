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
            'name' => 'Курсы',
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
            'name' => 'Сайт-Каталог',
            'service_category_id' => 1,
            'price' => 25000,
            'work_days_duration' => '30 (тридцать) рабочих дней',
            'description' => 'описание Инд каталог',
            'deal_template_ids' => json_encode([
                'law_default' => 70,
                'law_complex' => 232,
                'physic_default' => 34,
                'physic_complex' => 250
            ]),
        ]);
        Service::create([
            'name' => 'Интернет-магазин',
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


        $services = Service::all();

        $clients = Client::all();

        $users = Department::getMainSaleDepartment()->activeUsers();

        foreach ($clients as $key => $client) {
            $contractData = [
                'number' => $key + 1,
                'phone' => '+8-(999)-999-99-99',
                'amount_price' => rand(100000, 500000),
                'client_id' => $client->id,
            ];


            $contract = Contract::create($contractData);


            $contractServices = $services->random(rand(1, 5));
            foreach ($contractServices as $service) {
                $contract->services()->attach($service->id, [
                    'price' => $service->price,
                ]);
            }
            $payments = array_map(function () {
                return rand(10000, 100000);
            }, range(1, rand(2, 5)));


            $this->addPaymentsToContract($contract, $payments);

            if ($users->count() > 0) {
                $randomUser = $users->random();
                
                $randomUser->contracts()->attach($contract->id, [
                    'role' => ContractUser::SALLER,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
    private function addPaymentsToContract(Contract $contract, array $payments, int $maxPayments = 5)
    {
        $order = 1;
        foreach ($payments as $key => $payment) {
            if($key == 0){
                $statuses = [Payment::STATUS_WAIT, Payment::STATUS_CLOSE];
                $idx = array_rand($statuses);
                $status = $statuses[$idx];
                $status = Payment::STATUS_CLOSE;
                $created_at = now()->addDays(rand(1, 10));
                $type = Payment::TYPE_NEW;
            }elseif($key == 1){
                $status = Payment::STATUS_WAIT;
                $created_at = null;
                $created_at = now()->addDays(rand(1, 10));
                $payment = 5000;
            }else{
                $status = Payment::STATUS_WAIT;
                $created_at = null;
                $created_at = now()->addDays(rand(1, 10));
            }

            if (!empty($payment) && $order <= $maxPayments) {
                $contract->payments()->create([
                    'value' => $payment,
                    'status' => $status,
                    'order' => $order,
                    'created_at' => $created_at,
                    'type' => $type,
                ]);
                $order++;
            }
        }
    }
}
 