<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@mail.ru',
            'password' => Hash::make('1409199696Rust'), // Никогда не храните пароли в открытом виде
            'role' => 'admin',
            'position_id' => null, // или установите в соответствующее значение
        ]);
    }
}
