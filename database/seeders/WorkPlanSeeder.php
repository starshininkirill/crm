<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
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


        // Двойной план продажников
        WorkPlan::create([
            'type' => WorkPlan::DOUBLE_PLAN,
            'department_id' => 1,
            'bonus' => 5000
        ]);

        // Бонус план продажников
        WorkPlan::create([
            'type' => WorkPlan::BONUS_PLAN,
            'department_id' => 1,
            'goal' => 150000,
            'bonus' => 2000
        ]);

        // Недельный план продажников
        WorkPlan::create([
            'type' => WorkPlan::WEEK_PLAN,
            'department_id' => 1,
            'bonus' => 1000
        ]);

        // Супер план продажников
        WorkPlan::create([
            'type' => WorkPlan::SUPER_PLAN,
            'department_id' => 1,
            'goal' => 430000,
            'bonus' => 2000
        ]);

        // Б1 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B1_PLAN,
            'department_id' => 1,
            'goal' => 2,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::INDIVIDUAL_SITE)->first()->id,
            'bonus' => 10
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B1_PLAN,
            'department_id' => 1,
            'goal' => 4,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::READY_SITE)->first()->id,
            'bonus' => 10
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B1_PLAN,
            'department_id' => 1,
            'goal' => 5,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::RK)->first()->id,
            'bonus' => 10
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B1_PLAN,
            'department_id' => 1,
            'goal' => 2,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::SEO)->first()->id,
            'bonus' => 10
        ]);

        // Б2 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B2_PLAN,
            'department_id' => 1,
            'goal' => 5,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::INDIVIDUAL_SITE)->first()->id,
            'bonus' => 7
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B2_PLAN,
            'department_id' => 1,
            'goal' => 6,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::READY_SITE)->first()->id,
            'bonus' => 7
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B2_PLAN,
            'department_id' => 1,
            'goal' => 7,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::RK)->first()->id,
            'bonus' => 7
        ]);
        WorkPlan::create([
            'type' => WorkPlan::B2_PLAN,
            'department_id' => 1,
            'goal' => 3,
            'service_category_id' => ServiceCategory::where('type', ServiceCategory::SEO)->first()->id,
            'bonus' => 7
        ]);

        // Б3 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B3_PLAN,
            'department_id' => 1,
            'goal' => 60,
            'bonus' => 7000
        ]);

        // Б4 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B4_PLAN,
            'department_id' => 1,
            'goal' => 10,
            'bonus' => 10000
        ]);

        // Процентная лестница продажников

        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'goal' => 60000,
            'bonus' => 3
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'goal' => 150000,
            'bonus' => 5
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'goal' => 290000,
            'bonus' => 7
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'goal' => 430000,
            'bonus' => 9
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'goal' => 430000,
            'bonus' => 9.5
        ]);

    }
}
