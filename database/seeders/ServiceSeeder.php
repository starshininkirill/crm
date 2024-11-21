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
            'work_days_duration' => 5,
            'description' => 'описание Инд лендинг'
        ]);
        Service::create([
            'name' => 'Инд Каталог',
            'service_category_id' => 1,
            'price' => 25000,
            'work_days_duration' => 10,
            'description' => 'описание Инд каталог'
        ]);
        Service::create([
            'name' => 'Инд Интернет магазин',
            'service_category_id' => 1,
            'price' => 50000,
            'work_days_duration' => 15,
            'description' => 'описание Инд ИМ'
        ]);
        Service::create([
            'name' => 'Гот Лендинг',
            'service_category_id' => 2,
            'price' => 7500,
            'description' => 'описание Гот лендинг'
        ]);
        Service::create([
            'name' => 'Гот Каталог',
            'service_category_id' => 2,
            'price' => 15000,
            'work_days_duration' => 4,
            'description' => 'описание Гот Каталог'
        ]);
        Service::create([
            'name' => 'Первичная настройка + ведение',
            'service_category_id' => 3,
            'price' => 15000,
            'work_days_duration' => 4,
            'description' => 'описание рк ведение и настройка'
        ]);
        Service::create([
            'name' => 'Ведение РК',
            'service_category_id' => 3,
            'price' => 10000,
            'description' => 'описание Ведение РК'
        ]);
        Service::create([
            'name' => 'Настройка РК',
            'service_category_id' => 3,
            'price' => 7500,
            'work_days_duration' => 1,
            'description' => 'описание Настройка РК'
        ]);
        Service::create([
            'name' => 'Базовое SEO',
            'service_category_id' => 4,
            'price' => 10000,
            'work_days_duration' => 1,
            'description' => 'описание Базовое СЕО'
        ]);
        Service::create([
            'name' => 'Внутреннее SEO',
            'service_category_id' => 4,
            'price' => 10000,
            'work_days_duration' => 1,
            'description' => 'описание Внутреннее СЕО'
        ]);
        Service::create([
            'name' => 'Вариативная карточка товара',
            'service_category_id' => 5,
            'price' => 5000,
            'description' => 'описание Вариативная карточка товара'
        ]);
        Service::create([
            'name' => 'Калькулятор',
            'service_category_id' => 5,
            'price' => 10000,
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
 