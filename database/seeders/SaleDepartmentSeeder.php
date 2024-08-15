<?php

namespace Database\Seeders;

use App\Models\Departments\Department;
use App\Models\Departments\Departmentable;
use App\Models\Departments\SaleDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SaleDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $saleDepartment = SaleDepartment::create(['name' => 'Отдел продаж']);
        $mainDepartment = $saleDepartment->department()->create();

        $saleDepartment1 = SaleDepartment::create(['name' => 'Подотдел продаж 1']);
        $saleDepartment2 = SaleDepartment::create(['name' => 'Подотдел продаж 2']);

        $saleDepartment1->department()->create(['parent_id' => $mainDepartment->id]);
        $saleDepartment2->department()->create(['parent_id' => $mainDepartment->id]);
    }
}
