<?php

namespace Database\Seeders\ContractSeeders;

use App\Models\Contracts\Client;
use App\Models\Contracts\Contract;
use App\Models\Contracts\ContractUser;
use App\Models\Contracts\Tarif;
use App\Models\Services\ServiceCategory;
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
        Carbon::setTestNow('2025-02-02 10:26:39');

        $clients = Client::factory()->count(3)->create();

        $rkCats = ServiceCategory::firstWhere('type', ServiceCategory::RK);
        $services = $rkCats->services;

        $department = Department::where('type', Department::DEPARTMENT_ADVERTISING)->whereNull('parent_id')->first();
        $users = $department->allUsers();

        $tarifs = Tarif::where('type', Tarif::TYPE_ADS)->get();

        foreach ($clients as $key => $client) {
            $tarif = $tarifs->random(1)->first();
            $contractData = [
                'number' => $key + 200,
                'phone' => '+8-(888)-888-88-88',
                'amount_price' => $tarif->optimal_price,
                'client_id' => $client->id,
                'type' => Contract::TYPE_ADS,
            ];

            $contract = Contract::create($contractData);

            $contractServices = $services->random(1);

            foreach ($contractServices as $service) {
                $contract->services()->attach($service->id, [
                    'price' => $service->price,
                ]);
            }
            
            $contract->serviceMonths()->create([
                'price' => $tarif->optimal_price,
                'tarif_id' => $tarif->id,
                'month' => 1,
                'start_service_date' => Carbon::now(),
                'end_service_date' => Carbon::now()->copy()->addMonth(),
            ]);

            $contract->serviceMonths()->create([
                'price' => $tarif->optimal_price,
                'tarif_id' => $tarif->id,
                'month' => 2,
                'start_service_date' => Carbon::now()->copy()->addMonth(),
                'end_service_date' => Carbon::now()->copy()->addMonth(2),
            ]);

            if ($users->count() > 0) {
                $randomUser = $users->random(1);

                $attachData = [
                    [
                        'id' => ContractUser::DIRECTOLOG,
                        'performers' => [$randomUser->id]
                    ]
                ];
                $contractService->attachPerformers($contract, $attachData);
            }
        }
    }
}
