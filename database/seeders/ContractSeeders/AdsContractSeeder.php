<?php

namespace Database\Seeders\ContractSeeders;

use App\Models\Contracts\Client;
use App\Models\Contracts\Contract;
use App\Models\Contracts\ContractUser;
use App\Models\Contracts\ServiceMonth;
use App\Models\Contracts\Tarif;
use App\Models\Finance\Payment;
use App\Models\Services\ServiceCategory;
use App\Models\States\Contract\Introduction;
use App\Models\UserManagement\Department;
use App\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AdsContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ContractService $contractService): void
    {
        // Carbon::setTestNow('2025-02-02 10:26:39');
        Carbon::setTestNow();

        $clients = Client::factory()->count(3)->create();

        $rkCats = ServiceCategory::firstWhere('type', ServiceCategory::RK);
        $services = $rkCats->services;

        $department = Department::where('type', Department::DEPARTMENT_ADVERTISING)->whereNull('parent_id')->first();
        $users = $department->allUsers()->filter(function($user) use ($department){
            return $user->id != $department->head_id;
        });

        $tarifs = Tarif::where('type', Tarif::TYPE_ADS)->get();

        foreach ($clients as $key => $client) {
            $tarif = $tarifs->random(1)->first();
            $contractData = [
                'number' => $key + 200,
                'phone' => '+8-(888)-888-88-88',
                'amount_price' => $tarif->optimal_price,
                'client_id' => $client->id,
                'type' => Contract::TYPE_ADS,
                'state' => Introduction::class
            ];

            $contract = Contract::create($contractData);

            $contractServices = $services->random(1);

            foreach ($contractServices as $service) {
                $contract->services()->attach($service->id, [
                    'price' => $service->price,
                ]);
            }
            $randomUser = $users->random(1)->first();


            $attachData = [
                [
                    'id' => ContractUser::DIRECTOLOG,
                    'performers' => [$randomUser->id]
                ]
            ];
            $contractService->attachPerformers($contract, $attachData);


            $firstMonth = $contract->serviceMonths()->create([
                'price' => $tarif->optimal_price,
                'tarif_id' => $tarif->id,
                'month' => 1,
                'start_service_date' => Carbon::now()->subMonth(),
                'end_service_date' => Carbon::now()->copy(),
                'user_id' => $randomUser->id,
                'type' => ServiceMonth::TYPE_ADS,
            ]);

            $payment = Payment::create([
                'value' => $tarif->optimal_price,
                'status' => Payment::STATUS_CLOSE,
            ]);
            $firstMonth->payment_id = $payment->id;
            $firstMonth->save();

            $contract->serviceMonths()->create([
                'price' => $tarif->optimal_price,
                'tarif_id' => $tarif->id,
                'month' => 2,
                'start_service_date' => Carbon::now()->copy(),
                'end_service_date' => Carbon::now()->copy()->addMonth(1),
                'user_id' => $randomUser->id,
                'type' => ServiceMonth::TYPE_ADS,
            ]);
        }
    }
}
