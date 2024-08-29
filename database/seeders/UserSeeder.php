<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'role' => 'admin',
            'position_id' => null,
        ]);
        $sale1 = User::create([
            'first_name' => 'Кирилл',
            'last_name' => 'Продажник',
            'email' => 'sale1@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 3,
            'department_id' => 2,
        ]);
        $sale2 = User::create([
            'first_name' => 'Илья',
            'last_name' => 'Продажник',
            'email' => 'sale2@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 2,
            'department_id' => 2,
        ]);

        // $users = User::factory()->count(5)->create();
    }
}
