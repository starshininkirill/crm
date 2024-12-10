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
            'name' => 'ИП 1',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 11111111111,
            'terminal' => 1,
            'template' => 288,
        ]);

        Organization::create([
            'name' => 'ИП 2',
            'nds' => Organization::WITHOUT_NDS,
            'inn' => 2222222222,
            'terminal' => 2,
            'template' => 290,
        ]);

        Organization::create([
            'name' => 'ООО',
            'nds' => Organization::WITH_NDS,
            'inn' => 33333333333,
            'terminal' => 3,
            'template' => 298,
        ]);

    }
}
