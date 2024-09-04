<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        PaymentMethod::create([
            'name' => 'СБП',
            'parent_id' => null, 
        ]);

        PaymentMethod::create([
            'name' => 'Карта',
            'parent_id' => null,
        ]);
        $parentType2 = PaymentMethod::create([
            'name' => 'РС',
            'parent_id' => null,
        ]);

        PaymentMethod::create([
            'name' => 'Наличные',
            'parent_id' => null,
        ]);


        PaymentMethod::create([
            'name' => 'ИП 1',
            'parent_id' => $parentType2->id,
        ]);

        PaymentMethod::create([
            'name' => 'ИП 2',
            'parent_id' => $parentType2->id,
        ]);
        PaymentMethod::create([
            'name' => 'ООО',
            'parent_id' => $parentType2->id,
        ]);
    }
}