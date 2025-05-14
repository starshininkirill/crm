<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Carbon::setTestNow('2025-01-01 10:26:39');
        // Создание месячных планов продажников
        $firstMonth = WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  150000,
                'month' => 1,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  220000,
                'month' => 2,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  290000,
                'month' => 3,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  300000,
                'month' => 4,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  300000,
                'month' => 5,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  300000,
                'month' => 6,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  350000,
                'month' => 7,
            ],
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  390000,
            ],
            'position_id' => 2,
            'department_id' => 1,
        ]);
        WorkPlan::create([
            'type' => WorkPlan::MOUNTH_PLAN,
            'data' => [
                'goal' =>  430000,
            ],
            'department_id' => 1,
            'position_id' => 3,
        ]);


        // Двойной план продажников
        WorkPlan::create([
            'type' => WorkPlan::DOUBLE_PLAN,
            'department_id' => 1,
            'data' => [
                'bonus' => 5000
            ],
        ]);

        // Бонус план продажников
        $bonusPlan = WorkPlan::create([
            'type' => WorkPlan::BONUS_PLAN,
            'department_id' => 1,
            'data' => [
                'goal' => 150000,
                'bonus' => 2000
            ],
        ]);

        // Недельный план продажников
        WorkPlan::create([
            'type' => WorkPlan::WEEK_PLAN,
            'department_id' => 1,
            'data' => [
                'bonus' => 1000
            ],
        ]);

        // Супер план продажников
        WorkPlan::create([
            'type' => WorkPlan::SUPER_PLAN,
            'department_id' => 1,
            'data' => [
                'goal' => 430000,
                'bonus' => 2000
            ],
        ]);

        // Б1 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B1_PLAN,
            'department_id' => 1,
            'data' => [
                'avgCountCalls' => 30,
                'avgDurationCalls' => 130,
                'goal' => 550000,
                'bonus' => 10,
            ],
        ]);

        // Б2 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B2_PLAN,
            'department_id' => 1,
            'data' => [
                "goal" => 5,
                "bonus" => "10000",
                "excludeIds" => [1, 4],
                "includeIds" => [7, 8, 9]
            ],
        ]);

        // Б3 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B3_PLAN,
            'department_id' => 1,
            'data' => [
                "goal" => "8",
                "bonus" => "10000",
                "includeIds" => [7, 8, 9, 6],
                "excludeServicePairs" => [
                    [1, 7],
                    [1, 9],
                    [1, 8],
                    [4, 7],
                    [4, 9],
                    [4, 8]
                ],
                "includedCategoryIds" => [1, 2]
            ]
        ]);

        // Б4 план продажников
        WorkPlan::create([
            'type' => WorkPlan::B4_PLAN,
            'department_id' => 1,
            'data' => [
                'includeIds' => [6],
                'goal' => 8,
                'bonus' => 10000,
            ],
        ]);

        // Процентная лестница продажников
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'data' => [
                'goal' => 60000,
                'bonus' => 3
            ],
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'data' => [
                'goal' => 150000,
                'bonus' => 5
            ],
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'data' => [
                'goal' => 290000,
                'bonus' => 7
            ],
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'data' => [
                'goal' => 430000,
                'bonus' => 9
            ],
        ]);
        WorkPlan::create([
            'type' => WorkPlan::PERCENT_LADDER,
            'department_id' => 1,
            'data' => [
                'bonus' => 9.5
            ],
        ]);

        WorkPlan::create([
            'type' => WorkPlan::NO_PERCENTAGE_MONTH,
            'department_id' => 1,
            'data' => [
                'goal' => 3,
            ]
        ]);

        Carbon::setTestNow();


        // $bonusPlan = WorkPlan::create([
        //     'type' => WorkPlan::BONUS_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 150000,
        //         'bonus' => 2000
        //     ],
        // ]);

        // $firstMonth = WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  150000,
        //         'month' => 1,
        //     ],
        //     'department_id' => 1,
        // ]);


        $bonusPlanData = $bonusPlan->data;
        $bonusPlanData['goal'] = 100000;
        $bonusPlanData['bonus'] = 1000;
        $bonusPlan->data = $bonusPlanData;
        $bonusPlan->save();

        $firstMonthData = $firstMonth->data;
        $firstMonthData['goal'] = 100000;
        $firstMonth->data = $firstMonthData;
        $firstMonth->save();



        // $lastMonth = Carbon::now()->startOfMonth()->subMonth()->format('Y-m-d');


        // // Создание месячных планов продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  150000,
        //         'month' => 1,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  220000,
        //         'month' => 2,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  290000,
        //         'month' => 3,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  300000,
        //         'month' => 4,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  300000,
        //         'month' => 5,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  300000,
        //         'month' => 6,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  350000,
        //         'month' => 7,
        //     ],
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  390000,
        //     ],
        //     'position_id' => 2,
        //     'department_id' => 1,
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::MOUNTH_PLAN,
        //     'data' => [
        //         'goal' =>  430000,
        //     ],
        //     'department_id' => 1,
        //     'position_id' => 3,
        //     'created_at' => $lastMonth,
        // ]);

        // // Двойной план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::DOUBLE_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'bonus' => 5000,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Бонус план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::BONUS_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 150000,
        //         'bonus' => 2000,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Недельный план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::WEEK_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'bonus' => 1000,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Супер план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::SUPER_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 430000,
        //         'bonus' => 2000,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // WorkPlan::create([
        //     'type' => WorkPlan::B1_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'avgCountCalls' => 30,
        //         'avgDurationCalls' => 130,
        //         'goal' => 550000,
        //         'bonus' => 10,

        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Б2 план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::B2_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         "goal" => 5,
        //         "bonus" => "10000",
        //         "excludeIds" => [1, 4],
        //         "includeIds" => [7, 8, 9]
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Б3 план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::B3_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         "goal" => "8",
        //         "bonus" => "10000",
        //         "includeIds" => [7, 8, 9, 6],
        //         "excludeServicePairs" => [
        //             [1, 7],
        //             [1, 9],
        //             [1, 8],
        //             [4, 7],
        //             [4, 9],
        //             [4, 8]
        //         ],
        //         "includedCategoryIds" => [1, 2]
        //     ],
        //     'created_at' => $lastMonth,
        // ]);



        // // Б4 план продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::B4_PLAN,
        //     'department_id' => 1,
        //     'data' => [
        //         'includeIds' => [6],
        //         'bonus' => 10000,
        //         'goal' => 8,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // // Процентная лестница продажников
        // WorkPlan::create([
        //     'type' => WorkPlan::PERCENT_LADDER,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 60000,
        //         'bonus' => 3
        //     ],
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::PERCENT_LADDER,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 150000,
        //         'bonus' => 5
        //     ],
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::PERCENT_LADDER,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 290000,
        //         'bonus' => 7
        //     ],
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::PERCENT_LADDER,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 430000,
        //         'bonus' => 9
        //     ],
        //     'created_at' => $lastMonth,
        // ]);
        // WorkPlan::create([
        //     'type' => WorkPlan::PERCENT_LADDER,
        //     'department_id' => 1,
        //     'data' => [
        //         'bonus' => 9.5
        //     ],
        //     'created_at' => $lastMonth,
        // ]);

        // WorkPlan::create([
        //     'type' => WorkPlan::NO_PERCENTAGE_MONTH,
        //     'department_id' => 1,
        //     'data' => [
        //         'goal' => 3,
        //     ],
        //     'created_at' => $lastMonth,
        // ]);
    }
}
