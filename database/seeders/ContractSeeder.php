<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Department;
use App\Models\Payment;
use App\Models\Service;
use App\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ContractService $contractService): void
    {
        Carbon::setTestNow('2025-04-01 10:26:39');
        $services = Service::query()->WhereNotNull('price')->get();

        $clients = Client::all();

        $users = Department::getMainSaleDepartment()->allUsers();

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

                $attachData = [
                    [
                        'id' => 0,
                        'performers' => [$randomUser->id]
                    ]
                ];
                $contractService->attachPerformers($contract, $attachData);
                // $contract->users()->attach($randomUser->id, ['role' => ContractUser::SALLER]);
            }
        }


        // TODO
        // Тестовые данные
        $firstContract = Contract::find(1);
        $firstContract->client->inn = '9999';
        $firstContract->client->save();

        Carbon::setTestNow();
    }

    private function addPaymentsToContract(Contract $contract, array $payments, int $maxPayments = 5)
    {
        $order = 1;
        foreach ($payments as $key => $payment) {
            if ($key == 0) {
                $statuses = [Payment::STATUS_WAIT, Payment::STATUS_CLOSE];
                $idx = array_rand($statuses);
                $status = $statuses[$idx];
                $status = Payment::STATUS_CLOSE;
                $created_at = now()->addDays(rand(1, 10));
                $type = Payment::TYPE_NEW;
            } elseif ($key == 1) {
                $status = Payment::STATUS_WAIT;
                $created_at = null;
                $created_at = now()->addDays(rand(1, 10));
                $payment = 5000;
            } else {
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
