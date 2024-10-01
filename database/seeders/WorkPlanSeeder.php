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
        WorkPlan::create([
            'value' =>  150000,
            'mounth' => 1,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  220000,
            'mounth' => 2,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  290000,
            'mounth' => 3,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  300000,
            'mounth' => 4,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  300000,
            'mounth' => 5,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  300000,
            'mounth' => 6,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  350000,
            'mounth' => 7,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'value' =>  390000,
            'department_id' => 1,
            'position_id' => 2,
        ]);
        WorkPlan::create([
            'value' =>  430000,
            'department_id' => 1,
            'position_id' => 3,
        ]);
    }
}
