<?php

namespace Database\Seeders;

use App\Models\Departments\AdvertisingDepartment;
use App\Models\Departments\Department;
use App\Models\Departments\SaleDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $saleDepartment = SaleDepartment::create(['name' => 'Отдел продаж']);
        $mainDepartment = $saleDepartment->department()->create();

        $salePositions = [
            ['name' => 'Руководитель отдела продаж', 'salary' => 100000],
            ['name' => 'Старший менеджер по продажам', 'salary' => 50000],
            ['name' => 'Менеджер по продажам', 'salary' => 25000],
        ];
    
        foreach ($salePositions as $position) {
            $mainDepartment->positions()->create(array_merge($position, ['department_id' => $mainDepartment->id]));
        }

        $saleDepartment1 = SaleDepartment::create(['name' => 'Подотдел продаж 1']);
        $saleDepartment2 = SaleDepartment::create(['name' => 'Подотдел продаж 2']);

        $saleDepartment1->department()->create(['parent_id' => $mainDepartment->id]);
        $saleDepartment2->department()->create(['parent_id' => $mainDepartment->id]);

        $advertisingDepartment = AdvertisingDepartment::create(['name' => 'Отдел Рекламы']);
        $mainDepartment = $advertisingDepartment->department()->create();

        $advertisingPositions = [
            ['name' => 'Руководитель отдела рекламы', 'salary' => 100000],
            ['name' => 'Старший менеджер по рекламе', 'salary' => 50000],
            ['name' => 'Менеджер по рекламе', 'salary' => 25000],
        ];
    
        foreach ($advertisingPositions as $position) {
            $mainDepartment->positions()->create(array_merge($position, ['department_id' => $mainDepartment->id]));
        }

    }
}
