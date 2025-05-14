<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Position;
use App\Models\Service;
use App\Models\User;
use App\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ContractService $contractService): void
    {
        Carbon::setTestNow('2025-01-01 10:26:39');

        $user = User::create([
            'first_name' => 'Мужичок',
            'last_name' => 'Тестовый',
            'surname' => 'Иванович',
            'work_phone' => 89999999999,
            'bitrix_id' => 1,
            'email' => 'test@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 4,
            'department_id' => 3,
            'created_at' => '2025-01-01 10:26:39'
        ]);


        $services = Service::query()->WhereNotNull('price')->get();

        $client = Client::find(4);
        $client->inn = '9999';
        $client->save();


        $contractData = [
            'number' => 22,
            'phone' => '+8-(999)-999-99-99',
            'amount_price' => 250000,
            'client_id' => $client->id,
        ];

        $contract = Contract::create($contractData);

        $contractServices = $services->random(5);

        foreach ($contractServices as $service) {
            $contract->services()->attach($service->id, [
                'price' => 50000,
            ]);
        }

        $contract->payments()->create([
            'value' => 100000,
            'status' => Payment::STATUS_CLOSE,
            'order' => 1,
            'created_at' => Carbon::now(),
            'type' => null,
        ]);

        $secondPayment = $contract->payments()->create([
            'value' => 50000,
            'status' => Payment::STATUS_WAIT,
            'order' => 1,
            'created_at' => Carbon::now(),
            'type' => null,
        ]);

        $thirdPayment = $contract->payments()->create([
            'value' => 50000,
            'status' => Payment::STATUS_WAIT,
            'order' => 1,
            'created_at' => Carbon::now(),
            'type' => null,
        ]);

        $last = $contract->payments()->create([
            'value' => 50000,
            'status' => Payment::STATUS_WAIT,
            'order' => 1,
            'created_at' => Carbon::now(),
            'type' => null,
        ]);

        $attachData = [
            [
                'id' => 0,
                'performers' => [$user->id]
            ]
        ];

        $contractService->attachPerformers($contract, $attachData);



        Carbon::setTestNow('2025-01-07 10:26:39');

        $secondPayment->created_at = Carbon::now();
        $secondPayment->status = Payment::STATUS_CLOSE;
        $secondPayment->type = Payment::TYPE_NEW;
        $secondPayment->save();

        Carbon::setTestNow('2025-02-03 10:26:39');

        $thirdPayment->created_at = Carbon::now();
        $thirdPayment->status = Payment::STATUS_CLOSE;
        $thirdPayment->type = Payment::TYPE_OLD;
        $thirdPayment->save();

        Carbon::setTestNow('2025-02-15 10:26:39');

        $user->fire();
        // dd($user);

        // $attachData = [
        //     [
        //         'id' => 0,
        //         'performers' => [1]
        //     ]
        // ];

        // $contractService->attachPerformers($contract, $attachData);

        Carbon::setTestNow('2025-03-20 10:26:39');

        $last->created_at = Carbon::now();
        $last->status = Payment::STATUS_CLOSE;
        $last->type = Payment::TYPE_OLD;
        $last->save();

        Carbon::setTestNow('2025-03-30 10:26:39');

        $position = Position::find(4);

        $position->salary = 50000;
        $position->save();

        Carbon::setTestNow();
    }
}
