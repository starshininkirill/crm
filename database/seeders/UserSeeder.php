<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\TimeCheck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
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

        // Создаем тестовых администраторов
        $this->createAdmins();

        // Создаем тестовых продажников
        $this->createSalesTeam();

        // Создаем случайных пользователей через фабрику
        // $this->createFactoryUsers(50); // 50 пользователей для каждого отдела

        // Создаем записи о рабочем времени
        $this->generateTimeChecks();
    }

    protected function createAdmins(): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');

        User::create([
            'first_name' => 'GRAMPUS',
            'last_name' => 'GRAMPUS',
            'surname' => 'GRAMPUS',
            'work_phone' => '89999999999',
            'bitrix_id' => 1,
            'email' => 'info@grampus-studio.ru',
            'password' => Hash::make('Goofy__741501'),
            'role' => 'admin',
        ]);

        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'surname' => 'Иванович',
            'work_phone' => '89999999999',
            'bitrix_id' => 1,
            'email' => 'admin@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'role' => 'admin',
            'position_id' => 6,
            'department_id' => 4,
        ]);

        Carbon::setTestNow('2025-03-02 10:26:39');
        $admin->update([
            'position_id' => 7,
            'department_id' => 4
        ]);
        Carbon::setTestNow();
    }

    protected function createSalesTeam(): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');

        // Продажники отдела 2
        $salesDept2 = [
            ['Кирилл', 'Продажник 1', 'sale1@mail.ru', 79535175470, 4],
            ['Илья', 'Продажник 1', 'sale2@mail.ru', 79922851746, 4],
            ['Игорь', 'Продажник 1', 'sale3@mail.ru', 79922857462, 2]
        ];

        foreach ($salesDept2 as $sale) {
            User::create([
                'first_name' => $sale[0],
                'last_name' => $sale[1],
                'surname' => 'Иванович',
                'work_phone' => '89999999999',
                'bitrix_id' => rand(2, 100),
                'email' => $sale[2],
                'password' => Hash::make('1409199696Rust'),
                'position_id' => $sale[4],
                'department_id' => 2,
                'phone' => $sale[3]
            ]);
        }

        // Продажники отдела 3
        $salesDept3 = [
            ['Вася', 'Продажник 2', 'sale4@mail.ru', null, 4],
            ['Костя', 'Продажник 2', 'sale5@mail.ru', null, 2],
            ['Евгений', 'Продажник 2', 'sale6@mail.ru', null, 3]
        ];

        foreach ($salesDept3 as $sale) {
            User::create([
                'first_name' => $sale[0],
                'last_name' => $sale[1],
                'surname' => 'Иванович',
                'work_phone' => '89999999999',
                'bitrix_id' => rand(2, 100),
                'email' => $sale[2],
                'password' => Hash::make('1409199696Rust'),
                'position_id' => $sale[4],
                'department_id' => 3,
                'phone' => $sale[3]
            ]);
        }

        // Руководители отделов
        $this->createDepartmentHeads();

        Carbon::setTestNow();
    }

    protected function createDepartmentHeads(): void
    {
        $sale1Head = User::create([
            'first_name' => 'Руководитель',
            'last_name' => 'Sale 1',
            'surname' => 'продажнивич',
            'work_phone' => '89999999999',
            'bitrix_id' => 10,
            'email' => 'sale-head@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 1,
            'department_id' => 2,
        ]);

        Department::find(2)->update(['head_id' => $sale1Head->id]);

        $sale2Head = User::create([
            'first_name' => 'Руководитель',
            'last_name' => 'Sale 2',
            'surname' => 'продажнивич',
            'work_phone' => '89999999999',
            'bitrix_id' => 10,
            'email' => 'sale2-head@mail.ru',
            'password' => Hash::make('1409199696Rust'),
            'position_id' => 1,
            'department_id' => 3,
        ]);

        Department::find(3)->update(['head_id' => $sale2Head->id]);
    }

    protected function createFactoryUsers(int $countPerDepartment): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');
        // Создаем пользователей для отдела 2
        User::factory()->count($countPerDepartment)->create([
            'department_id' => 2,
            'surname' => 'Иванович',
            'position_id' => rand(2, 4), // случайная позиция из доступных для продажников
            'role' => 'user'
        ]);

        // Создаем пользователей для отдела 3
        User::factory()->count($countPerDepartment)->create([
            'department_id' => 3,
            'surname' => 'Иванович',
            'position_id' => rand(2, 4),
            'role' => 'user'
        ]);

        // Создаем пользователей с реальными номерами
        // foreach ($realManagersNumbers as $number) {
        //     User::factory()->create([
        //         'phone' => $number,
        //         'department_id' => rand(2, 3),
        //         'position_id' => rand(2, 4),
        //     ]);
        // }
    }

    protected function generateTimeChecks(): void
    {
        Carbon::setTestNow();
        $users = User::all();
        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($users as $user) {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                if ($date->isWeekend()) continue;

                $user->timeChecks()->createMany([
                    ['date' => $date->copy()->setTime(9, 0), 'action' => TimeCheck::ACTION_START],
                    ['date' => $date->copy()->setTime(18, 0), 'action' => TimeCheck::ACTION_END],
                ]);
            }
        }
    }
}
