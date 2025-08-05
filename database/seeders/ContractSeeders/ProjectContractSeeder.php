<?php

namespace Database\Seeders\ContractSeeders;

use App\Models\Contracts\Client;
use App\Models\Contracts\Contract;
use App\Models\Contracts\ContractUser;
use App\Models\UserManagement\Department;
use App\Models\Finance\Payment;
use App\Models\Services\Service;
use App\Models\States\Contract\Close;
use App\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectContractSeeder extends Seeder
{
    public function run(ContractService $contractService): void
    {
        $this->createUpsails($contractService);
        $this->attachManagersToExistingContracts($contractService);
    }

    private function attachManagersToExistingContracts(ContractService $contractService)
    {
        Carbon::setTestNow();
        $startDate = Carbon::now()->subMonth(2);
        $endDate = Carbon::now();

        $projectDepartment = Department::where('type', Department::DEPARTMENT_PROJECT_MANAGERS)->whereNull('parent_id')->first();
        if (!$projectDepartment) {
            return;
        }
        $projectManagers = $projectDepartment->allUsers(Carbon::now())->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        });

        Carbon::setTestNow($startDate);
        
        $managerToFire = $projectManagers->first();
        $managerToFire->fire();

        Carbon::setTestNow();


        $managersIds = $projectManagers->pluck('id');

        $contracts = Contract::whereHas('contractUsers', function ($query) use ($managersIds) {
            $query->where('role', ContractUser::SELLER)->whereNotIn('user_id', $managersIds);
        })->whereDoesntHave('contractUsers', function ($query) {
            $query->where('role', ContractUser::PROJECT);
        })->get();

        foreach ($contracts as $contract) {
            if ($projectManagers->isEmpty()) {
                continue;
            }

            $totalDays = $startDate->copy()->diffInDays($endDate);
            $randomDays = rand(0, $totalDays);
            $randomDate = $startDate->copy()->addDays($randomDays);
            Carbon::setTestNow($randomDate);

            $attachData = [
                [
                    'id' => ContractUser::PROJECT,
                    'performers' => [$projectManagers->random()->id]
                ]
            ];
            $contractService->attachPerformers($contract, $attachData);

            $contract->state->transitionTo(Close::class);
            $contract->payments()->update(['status' => Payment::STATUS_CLOSE]);
            $contract->close_date = Carbon::now();
            $contract->is_complex = (bool) rand(0, 1);
            $contract->save();
        }
    }

    private function createUpsails(ContractService $contractService)
    {
        Carbon::setTestNow('2025-07-01 10:26:39');
        $clients = Client::factory()->count(30)->create();

        Carbon::setTestNow();
        $startDate = Carbon::now()->subMonth(2);
        $endDate = Carbon::now();

        $services = Service::query()->WhereNotNull('price')->get();
        $department = Department::where('type', Department::DEPARTMENT_PROJECT_MANAGERS)->whereNull('parent_id')->first();

        if (!$department) {
            return;
        }

        $users = $department->allUsers($endDate);

        foreach ($clients as $key => $client) {

            $totalDays = $startDate->copy()->diffInDays($endDate);
            $randomDays = rand(0, $totalDays);
            $randomDate = $startDate->copy()->addDays($randomDays);
            Carbon::setTestNow($randomDate);


            $contractData = [
                'number' => $key + 100,
                'phone' => '+8-(888)-888-88-88',
                'amount_price' => rand(50000, 200000),
                'client_id' => $client->id,
            ];

            $contract = Contract::create($contractData);

            $contractServices = $services->random(1);

            foreach ($contractServices as $service) {
                $contract->services()->attach($service->id, [
                    'price' => $service->price,
                ]);
            }

            $contract->payments()->create([
                'value' => rand(10000, 50000),
                'status' => Payment::STATUS_CLOSE,
                'order' => 1,
                'type' => Payment::TYPE_NEW,
            ]);


            if ($users->count() > 0) {
                $randomUser = $users->random();

                $attachData = [
                    [
                        'id' => ContractUser::SELLER,
                        'performers' => [$randomUser->id]
                    ]
                ];
                $contractService->attachPerformers($contract, $attachData);
            }
        }
        Carbon::setTestNow();
    }
}
