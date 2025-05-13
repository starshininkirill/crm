<?php

namespace Database\Seeders;

use App\Models\AdvertisingDepartment;
use App\Models\Department;
use App\Models\Position;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainDepartment = Department::create([
            'name' => 'Отдел продаж',
            'type' => Department::SALE_DEPARTMENT
        ]);

        $mainDepartment->save();

        $saleDepartment1 = Department::create([
            'name' => 'Подотдел продаж 1',
            'type' => Department::SALE_DEPARTMENT,
            'parent_id' => $mainDepartment->id,
        ]);

        $saleDepartment2 = Department::create([
            'name' => 'Подотдел продаж 2',
            'type' => Department::SALE_DEPARTMENT,
            'parent_id' => $mainDepartment->id,
        ]);

        $mainDepartment = Department::create([
            'name' => 'Отдел рекламы',
            'type' => Department::ADVERTISING_DEPARTMENT,
        ]);
    }
}
