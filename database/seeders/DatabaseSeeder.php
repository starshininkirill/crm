<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            OptionSeeder::class,

            UserSeeder::class,
            ClientSeeder::class,
            ServiceSeeder::class,
            PaymentMethodSeeder::class,
            WorkPlanSeeder::class,
            OrganizationSeeder::class,
        ]);
    }
}
