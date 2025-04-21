<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::create([
            'short_name' => 'ИП 1',
            'name' => 'Индивидуальный предприниматель НИКОЛАЕВ КИРИЛЛ АЛЕКСАНДРОВИЧ',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 352531026755,
            'terminal' => 1,
        ]);

        Organization::create([
            'short_name' => 'ИП 2',
            'name' => 'Индивидуальный предприниматель НИКОЛАЕВА ЕЛЕНА ВАСИЛЬЕВНА',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 352512760417,
            'terminal' => 2,
        ]);

        Organization::create([
            'short_name' => 'ООО',
            'name' => 'ООО ЭДДИ ГРУПП',
            'nds' => Organization::WITH_NDS,
            'inn' => 3500007401,
            'terminal' => 3,
        ]);
        Organization::create([
            'short_name' => 'ИП 4',
            'name' => 'Индивидуальный предприниматель 4',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 888888888,
            'terminal' => 5,
        ]);
        Organization::create([
            'short_name' => 'ИП 5',
            'name' => 'Индивидуальный предприниматель 5',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 7777777777,
            'terminal' => 6,
        ]);
        Organization::create([
            'short_name' => 'ИП 6',
            'name' => 'Индивидуальный предприниматель 6',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 6666666666,
            'terminal' => 7,
        ]);
    }
}
