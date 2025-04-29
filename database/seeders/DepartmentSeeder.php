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

        $salePositions = [
            ['name' => 'Руководитель отдела продаж', 'salary' => 100000],
            ['name' => 'Ведущий менеджер по продажам', 'salary' => 50000],
            ['name' => 'Главный менеджер по продажам', 'salary' => 25000],
            ['name' => 'Менеджер по продажам', 'salary' => 25000],
        ];

        $mainDepartment->save();

        foreach ($salePositions as $position) {
            Position::create($position);
        };

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

        $advertisingPositions = [
            ['name' => 'Руководитель отдела рекламы', 'salary' => 100000],
            ['name' => 'Старший менеджер по рекламе', 'salary' => 50000],
            ['name' => 'Менеджер по рекламе', 'salary' => 25000],
        ];

        foreach ($advertisingPositions as $position) {
            Position::create($position);
        }
    }
}
