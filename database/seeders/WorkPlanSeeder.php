<?php

namespace Database\Seeders;

use App\Models\WorkPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Создание месячных планов продажников
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  150000,
            'mounth' => 1,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  220000,
            'mounth' => 2,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  290000,
            'mounth' => 3,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  300000,
            'mounth' => 4,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  300000,
            'mounth' => 5,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  300000,
            'mounth' => 6,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  350000,
            'mounth' => 7,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  390000,
            'department_id' => 1,
            'position_id' => 2,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'goal' =>  430000,
            'department_id' => 1,
            'position_id' => 3,
        ]);
    }
}
