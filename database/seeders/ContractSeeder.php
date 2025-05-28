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
        Carbon::setTestNow();
        $startDate = Carbon::now()->subMonths(5);
        $endDate = Carbon::now();

        $services = Service::query()->WhereNotNull('price')->get();

        $clients = Client::all();

        $users = Department::getMainSaleDepartment()->allUsers($endDate, ['departmentHead'])->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        })->values();

        foreach ($clients as $key => $client) {

            $totalDays = $startDate->copy()->diffInDays($endDate); // Количество дней между startDate и endDate
            $randomDays = rand(0, $totalDays); // Случайное число дней от 0 до totalDays
            $randomDate = $startDate->copy()->addDays($randomDays);
            Carbon::setTestNow($randomDate);

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


            $this->addPaymentsToContract($contract, $payments, $randomDate);

            if ($users->count() > 0) {
                $randomUser = $users->random();

                $attachData = [
                    [
                        'id' => 0,
                        'performers' => [$randomUser->id]
                    ]
                ];
                Carbon::setTestNow('2025-01-02 10:26:39');
                $contractService->attachPerformers($contract, $attachData);
            }
        }

        // TODO
        // Тестовые данные
        $firstContract = Contract::find(1);
        $firstContract->client->inn = '9999';
        $firstContract->client->save();

        Carbon::setTestNow();
    }

    private function addPaymentsToContract(Contract $contract, array $payments, Carbon $startDate)
    {
        $maxPayments = 5;
        $order = 1;
        $lastStatus = null;
        $currentPaymentDate = clone $startDate;

        // Получаем реальную текущую дату
        Carbon::setTestNow();
        $realNow = Carbon::now();

        if ($currentPaymentDate->isToday()) {
            $currentPaymentDate->addDay();
        }

        foreach ($payments as $payment) {
            if ($order > $maxPayments) break;

            if ($currentPaymentDate->greaterThan($realNow)) {
                break;
            }

            $type = $currentPaymentDate->month === $startDate->month
                ? Payment::TYPE_NEW
                : Payment::TYPE_OLD;

            if ($order === 1) {
                $status = Payment::STATUS_CLOSE;
            } else {
                if ($lastStatus === Payment::STATUS_CLOSE) {
                    $chance = rand(1, 100);
                    $status = $chance <= 55 ? Payment::STATUS_CLOSE : Payment::STATUS_WAIT;
                } else {
                    $status = Payment::STATUS_WAIT;
                }
            }

            if (!empty($payment)) {
                // Устанавливаем временную дату только перед созданием записи
                Carbon::setTestNow($currentPaymentDate);

                $contract->payments()->create([
                    'value' => $payment,
                    'status' => $status,
                    'order' => $order,
                    'type' => $type,
                ]);

                $order++;
                $lastStatus = $status;
            }

            // Подготавливаем следующую дату
            $currentPaymentDate = $currentPaymentDate->copy()->addDays(10);

            if ($currentPaymentDate->isToday()) {
                $currentPaymentDate->addDay();
            }
        }

        // Сбрасываем тестовую дату после завершения
        Carbon::setTestNow();
    }
}
