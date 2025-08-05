<?php

namespace Database\Seeders;

use Database\Seeders\ContractSeeders\AdsContractSeeder;
use Database\Seeders\ContractSeeders\ProjectContractSeeder;
use Database\Seeders\ContractSeeders\SaleContractSeeder;
use Database\Seeders\DocumentGeneratorSeeder\DocumentSelectionRuleSeeder;
use Database\Seeders\DocumentGeneratorSeeder\DocumentTemplateSeeder;
use Database\Seeders\GlobalsSeeder\CallHistorySeeder;
use Database\Seeders\GlobalsSeeder\DepartmentSeeder;
use Database\Seeders\GlobalsSeeder\OptionSeeder;
use Database\Seeders\GlobalsSeeder\OrganizationSeeder;
use Database\Seeders\GlobalsSeeder\ServiceSeeder;
use Database\Seeders\GlobalsSeeder\TarifSeeder;
use Database\Seeders\GlobalsSeeder\WorkPlanSeeder;
use Database\Seeders\GlobalsSeeder\WorkStatusSeeder;
use Database\Seeders\UserSeeders\EmploymentTypeSeeder;
use Database\Seeders\UserSeeders\PositionSeeder;
use Database\Seeders\UserSeeders\TestSeeder;
use Database\Seeders\UserSeeders\UserSeeder;
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
            ServiceSeeder::class,
            TarifSeeder::class,

            SaleContractSeeder::class,
            ProjectContractSeeder::class,
            AdsContractSeeder::class,

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
