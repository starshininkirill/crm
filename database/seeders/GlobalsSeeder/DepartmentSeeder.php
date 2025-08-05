<?php

namespace Database\Seeders\GlobalsSeeder;

use App\Models\AdvertisingDepartment;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\Position;
use App\Models\Services\ServiceCategory;
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

        $this->reklamaDepartment();
    }

    protected function projectManagersDepartment()
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $mainDepartment = Department::create([
            'name' => 'Отдел Сопровождения',
            'type' => Department::DEPARTMENT_PROJECT_MANAGERS
        ]);
    }

    protected function saleDepartments()
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

        Carbon::setTestNow();
    }

    protected function reklamaDepartment(): void
    {
        Carbon::setTestNow('2025-01-01 00:00:00');
        $mainDepartment = Department::create([
            'name' => 'Отдел рекламы',
            'type' => Department::DEPARTMENT_ADVERTISING,
        ]);

        $mainDepartment->save();

        $reklamaDepartment1 = Department::create([
            'name' => 'Подотдел Рекламы 1',
            'type' => Department::DEPARTMENT_ADVERTISING,
            'parent_id' => $mainDepartment->id,
        ]);

        $reklamaDepartment2 = Department::create([
            'name' => 'Подотдел Рекламы 2',
            'type' => Department::DEPARTMENT_ADVERTISING,
            'parent_id' => $mainDepartment->id,
        ]);


        Carbon::setTestNow();
    }

}
