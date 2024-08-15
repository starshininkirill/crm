<?php

namespace Database\Seeders;

use App\Models\Departments\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Department::create(['name' => 'Отдел продаж']);
        Department::create(['name' => 'Отдел рекламы']);
    }
}
