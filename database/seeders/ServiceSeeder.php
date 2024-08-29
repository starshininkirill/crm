<?php

namespace Database\Seeders;

use App\Models\Departments\SaleDepartment;
use App\Models\Client; 
use App\Models\Contract; 
use App\Models\Service;
use App\Models\Payment;
use App\Models\RoleInContract;
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


        $services = Service::all();

        $clients = Client::all();

        $users = SaleDepartment::getMainDepartment()->activeUsers();

        foreach ($clients as $client) {
            $contractData = [
                'number' => strtoupper(uniqid()),
                'amount_price' => rand(10000, 50000),
                'comment' => 'Auto-generated contract',
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
                return rand(1000, 10000);
            }, range(1, rand(1, 5)));

            $this->addPaymentsToContract($contract, $payments);

            if ($users->count() > 0) {
                $randomUser = $users->random();
                $roleInContractId = RoleInContract::where('is_saller', RoleInContract::IS_SALLER)->value('id');
                
                $randomUser->contracts()->attach($contract->id, [
                    'role_in_contracts_id' => $roleInContractId,
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
                $statuses = [Payment::STATUS_OPEN, Payment::STATUS_CLOSE];
                $idx = array_rand($statuses);
                $status = $statuses[$idx];
                $confirmed_at = now();
                $type = Payment::TYPE_NEW;
            }else{
                $status = Payment::STATUS_OPEN;
                $confirmed_at = null;
                $confirmed_at = now();
                $type = Payment::TYPE_OLD;
            }

            if (!empty($payment) && $order <= $maxPayments) {
                $contract->payments()->create([
                    'value' => $payment,
                    'status' => $status,
                    'order' => $order,
                    'confirmed_at' => $confirmed_at,
                    'type' => $type
                ]);
                $order++;
            }
        }
    }
}
 