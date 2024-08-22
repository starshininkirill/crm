<?php

namespace Database\Seeders;

use App\Models\RoleInContract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleInContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoleInContract::create([
            'name' => 'Ответственный',
            'is_saller' => RoleInContract::IS_SALLER
        ]);
        RoleInContract::create([
            'name' => 'Проект-менеджер',
        ]);
        RoleInContract::create([
            'name' => 'Дизайнер',
        ]);
        RoleInContract::create([
            'name' => 'Програмист',
        ]);
        RoleInContract::create([
            'name' => 'Контент-специалист',
        ]);
        RoleInContract::create([
            'name' => 'Директолог',
            'department_id' => 4
        ]);
        RoleInContract::create([
            'name' => 'SEO специалист',
        ]);
    }
}
 