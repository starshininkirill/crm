<?php

namespace Database\Seeders;

use App\Models\Departments\AdvertisingDepartment;
use App\Models\Departments\Department;
use App\Models\Departments\SaleDepartment;
use App\Models\ServiceCategory;
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
            ['name' => 'Ведущий менеджер по продажам', 'salary' => 50000],
            ['name' => 'Главный менеджер по продажам', 'salary' => 25000],
            ['name' => 'Менеджер по продажам', 'salary' => 25000],
        ];

        $saleDepartment->plans = [
            'double_plan' => [
                'bonus' => 5000
            ],
            'bonus_plan' => [
                'goal' => 150000,
                'bonus' => 2000
            ],
            'week_plan' => [
                'bonus' => 1000
            ],
            'super_plan' => [
                'goal' => 430000,
                'bonus' => 2000
            ],
            'b1' => [
                'goal' => [
                    ServiceCategory::INDIVIDUAL_SITE => 2,
                    ServiceCategory::READY_SITE => 4,
                    ServiceCategory::RK => 5,
                    ServiceCategory::SEO => 2,
                ],
                'bonus' => 10
            ],
            'b2' => [
                'goal' => [
                    ServiceCategory::INDIVIDUAL_SITE => 5,
                    ServiceCategory::READY_SITE => 6,
                    ServiceCategory::RK => 7,
                    ServiceCategory::SEO => 3,
                ],
                'bonus' => 7
            ],
            'b3' => [
                'goal' => 60,
                'bonus' => 7000
            ],
            'b4' => [
                'goal' => 10,
                'bonus' => 10000
            ],
            'percent' => [
                0 => 3,
                60000 => 5,
                150000 => 7,
                290000 => 9,
                430000 => 9.5,
            ]

        ];

        $saleDepartment->save();



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
