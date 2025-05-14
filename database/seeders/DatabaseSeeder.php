<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            PositionSeeder::class,
            OptionSeeder::class,

            EmploymentTypeSeeder::class,
            UserSeeder::class,
            ClientSeeder::class,
            ServiceSeeder::class,
            ContractSeeder::class,
            WorkPlanSeeder::class,
            OrganizationSeeder::class,
            DocumentTemplateSeeder::class,
            DocumentSelectionRuleSeeder::class,
            CallHistorySeeder::class,
            WorkStatusSeeder::class,

            TestSeeder::class,
        ]);
    }
}
