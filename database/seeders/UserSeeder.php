<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $realManagersNumbers = [
            79922896554,
            79535174387,
            79922802826,
            79005325832,
            79922857462,
            79951460603,
            79922893028,
            79922889975,
            79922833926,
            79954943528,
            79922871233,
            79535175470,
            79922865076,
            79922802313,
            79535174769,
            79922883450,
            79922806049,
            79535175529,
            79005479481,
            79922851746,
        ];



        Carbon::setTestNow('2025-01-02 10:26:39');
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'surname' => 'Иванович',
            'work_phone' => 89999999999,
            'bitrix_id' => 1,
            'email' => 'admin@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'role' => 'admin',
            'position_id' => 6,
            'department_id' => 1,
            'created_at' => '2025-03-02 10:26:39'
        ]);
        Carbon::setTestNow();

        Carbon::setTestNow('2025-03-02 10:26:39');
        $admin->position_id = 7;
        $admin->department_id = 4;
        $admin->save();
        Carbon::setTestNow();

        Carbon::setTestNow('2025-04-01 10:26:39');
        $sale1 = User::create([
            'first_name' => 'Кирилл',
            'last_name' => 'Продажник',
            'surname' => 'Иванович',
            'work_phone' => 89999999999,
            'bitrix_id' => 2,
            'email' => 'sale1@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 1,
            'department_id' => 2,
            'phone' => 79535175470
        ]);
        $sale2 = User::create([
            'first_name' => 'Илья',
            'last_name' => 'Продажник',
            'surname' => 'Иванович',
            'work_phone' => 89999999999,
            'bitrix_id' => 3,
            'email' => 'sale2@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 2,
            'department_id' => 2,
            'phone' => 79922851746
        ]);

        $sale2 = User::create([
            'first_name' => 'Игорь',
            'last_name' => 'Продажник 2 отд',
            'surname' => 'Иванович',
            'work_phone' => 89999999999,
            'bitrix_id' => 4,
            'email' => 'sale3@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 3,
            'department_id' => 2,
            'phone' => 79922857462
        ]);

        Carbon::setTestNow();

        // foreach ($realManagersNumbers as $number) {
        //     User::create([
        //         'first_name' => fake()->firstName(),
        //         'last_name' => fake()->lastName(),
        //         'email' => fake()->unique()->safeEmail(),
        //         'password' => Hash::make('password'), // Установите временный пароль
        //         'role' => 'user', // Роль по умолчанию
        //         'position_id' => null, // Можно установить позже
        //         'department_id' => 3, // Можно установить позже
        //         'phone' => $number, // Задаем номер телефона
        //     ]);
        // }
    }
}
