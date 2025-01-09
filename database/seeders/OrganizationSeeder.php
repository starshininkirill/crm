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
            'template' => 288,
            'active' => 1,
        ]);

        Organization::create([
            'short_name' => 'ИП 2',
            'name' => 'Индивидуальный предприниматель НИКОЛАЕВА ЕЛЕНА ВАСИЛЬЕВНА',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 352512760417,
            'terminal' => 2,
            'template' => 290,
            'active' => 1,
        ]);

        Organization::create([
            'short_name' => 'ООО',
            'name' => 'ООО ЭДДИ ГРУПП',
            'nds' => Organization::WITH_NDS,
            'inn' => 3500007401,
            'terminal' => 3,
            'template' => 298,
            'active' => 1,
        ]);

    }
}
