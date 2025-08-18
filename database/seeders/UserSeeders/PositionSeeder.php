<?php

namespace Database\Seeders\UserSeeders;

use App\Models\AdvertisingDepartment;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\Position;
use App\Models\Services\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->saleManagersPositions();
        $this->projectManagersPositions();
        $this->advertisingSalePositions();
    }

    private function advertisingSalePositions()
    {
        Carbon::setTestNow('2025-01-01 10:26:39');
        $position = [
            ['name' => 'Менеджер по продаже Рекламы', 'salary' => 20000, 'plan_type' => Position::PLAN_TYPE_ADVERTISING_SALE],
        ];

        foreach ($position as $position) {
            Position::create($position);
        };
    }

    private function projectManagersPositions()
    {
        Carbon::setTestNow('2025-01-01 10:26:39');
        $position = [
            ['name' => 'Руководитель отдела Сопровождения', 'salary' => 40000],
            ['name' => 'Проект-менеджер', 'salary' => 16000, 'has_probation' => true],
            ['name' => 'Старший проект-менеджер', 'salary' => 19000],
            ['name' => 'Ведущий проект-менеджер', 'salary' => 22000],
            ['name' => 'Главный проект-менеджер', 'salary' => 25000],
        ];

        foreach ($position as $position) {
            Position::create($position);
        };
    }

    private function saleManagersPositions()
    {
        Carbon::setTestNow('2025-01-01 10:26:39');
        $position = [
            ['name' => 'Руководитель отдела продаж', 'salary' => 100000],
            ['name' => 'Ведущий менеджер по продажам', 'salary' => 50000, 'has_probation' => true],
            ['name' => 'Главный менеджер по продажам', 'salary' => 25000, 'has_probation' => true],
            ['name' => 'Менеджер по продажам', 'salary' => 25000, 'has_probation' => true],
            ['name' => 'Старший менеджер по рекламе', 'salary' => 50000, 'has_probation' => true],
            ['name' => 'Менеджер по рекламе', 'salary' => 25000, 'has_probation' => true],
        ];

        foreach ($position as $position) {
            Position::create($position);
        };

        $position = Position::create([
            'name' => 'Руководитель отдела рекламы',
            'salary' => 50000,
            'created_at' => '2025-03-01 10:26:39'
        ]);
        Carbon::setTestNow();


        Carbon::setTestNow('2025-04-02 12:00:00');
        $position->salary = 75000;
        $position->save();
        Carbon::setTestNow();

        Carbon::setTestNow('2025-05-01 09:30:00');
        $position->salary = 100000;
        $position->save();
        Carbon::setTestNow();
    }
}
