<?php

namespace Database\Seeders;

use App\Models\AdvertisingDepartment;
use App\Models\Department;
use App\Models\Position;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->saleDepartments();

        $this->projectManagersDepartment();
    }

    private function projectManagersDepartment()
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $mainDepartment = Department::create([
            'name' => 'Отдел Сопровождения',
            'type' => Department::DEPARTMENT_PROJECT_MANAGERS
        ]);
    }

    private function saleDepartments()
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $mainDepartment = Department::create([
            'name' => 'Отдел продаж',
            'type' => Department::DEPARTMENT_SALE
        ]);

        $mainDepartment->save();

        $saleDepartment1 = Department::create([
            'name' => 'Подотдел продаж 1',
            'type' => Department::DEPARTMENT_SALE,
            'parent_id' => $mainDepartment->id,
        ]);

        $saleDepartment2 = Department::create([
            'name' => 'Подотдел продаж 2',
            'type' => Department::DEPARTMENT_SALE,
            'parent_id' => $mainDepartment->id,
        ]);

        $mainDepartment = Department::create([
            'name' => 'Отдел рекламы',
            'type' => Department::DEPARTMENT_ADVERTISING,
        ]);

        Carbon::setTestNow();
    }
}
