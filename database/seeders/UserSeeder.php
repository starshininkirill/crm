<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\TimeCheck;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    protected $employmentTypes;
    private static bool $anotherRecipientCreated = false;
    private static bool $compensationAssigned = false;

    public function run(): void
    {
        self::$anotherRecipientCreated = false;
        self::$compensationAssigned = false;
        $this->employmentTypes = EmploymentType::all();

        if ($this->employmentTypes->isEmpty()) {
            $this->command->warn('Таблица employment_types пуста. Заполните ее перед запуском сидера.');
            return;
        }

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

        $this->createProjectManagers();

        // Создаем случайных пользователей через фабрику
        // $this->createFactoryUsers(50); // 50 пользователей для каждого отдела

        // Создаем записи о рабочем времени
        $this->generateTimeChecks();
    }

    protected function createAdmins(): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');

        $user = User::create([
            'first_name' => 'GRAMPUS',
            'last_name' => 'GRAMPUS',
            'surname' => 'GRAMPUS',
            'work_phone' => '89999999999',
            'bitrix_id' => 1,
            'email' => 'info@grampus-studio.ru',
            'password' => Hash::make('Goofy__741501'),
            'role' => 'admin',
        ]);
        $this->createEmploymentDetailsForUser($user);

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
        $this->createEmploymentDetailsForUser($admin);

        Carbon::setTestNow('2025-03-02 10:26:39');
        $admin->update([
            'position_id' => 7,
            'department_id' => 4
        ]);
        Carbon::setTestNow();
    }

    protected function createProjectManagers(): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');
        // Сортудники
        $projectManagers = [
            ['Анастасия', 'Загоскина', 'zagoskina@mail.ru', 89999999944, 8],
            ['Дарья', 'Романова', 'dariaromanova@mail.ru', 89999999911, 12],
            ['Марина', 'Красильникова', 'krasilnikova@mail.ru', 89999999922, 9],
            ['Никита', 'Редькин', 'redkin@mail.ru', 89999999933, 11],
        ];

        $department = Department::firstWhere('type', Department::DEPARTMENT_PROJECT_MANAGERS);

        foreach ($projectManagers as $key => $person) {
            $user = User::create([
                'first_name' => $person[0],
                'last_name' => $person[1],
                'surname' => 'Иванович',
                'work_phone' => '89999999999',
                'bitrix_id' => rand(2, 100),
                'email' => $person[2],
                'password' => Hash::make('1409199696Rust'),
                'position_id' => $person[4],
                'department_id' => $department->id,
                'phone' => $person[3]
            ]);

            if ($key == 2) {
                $user->probation_start = Carbon::parse('2025-06-01')->startOfMonth();
                $user->probation_end = Carbon::parse('2025-06-01')->endOfMonth();
                $user->save();
            }

            if ($person[4] == 8) {
                $department->head_id = $user->id;
                $department->save();
            }

            $this->createEmploymentDetailsForUser($user);
        }
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
            $user = User::create([
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
            $this->createEmploymentDetailsForUser($user);
        }

        // Продажники отдела 3
        $salesDept3 = [
            ['Вася', 'Продажник 2', 'sale4@mail.ru', null, 4],
            ['Костя', 'Продажник 2', 'sale5@mail.ru', null, 2],
            ['Евгений', 'Продажник 2', 'sale6@mail.ru', null, 3]
        ];

        foreach ($salesDept3 as $sale) {
            $user = User::create([
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
            $this->createEmploymentDetailsForUser($user);
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
        $this->createEmploymentDetailsForUser($sale1Head);

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
        $this->createEmploymentDetailsForUser($sale2Head);

        Department::find(3)->update(['head_id' => $sale2Head->id]);
    }

    protected function createFactoryUsers(int $countPerDepartment): void
    {
        Carbon::setTestNow('2025-01-02 10:26:39');
        // Создаем пользователей для отдела 2
        User::factory()->count($countPerDepartment)
            ->afterCreating(function (User $user) {
                $this->createEmploymentDetailsForUser($user);
            })
            ->create([
                'department_id' => 2,
                'surname' => 'Иванович',
                'position_id' => rand(2, 4), // случайная позиция из доступных для продажников
                'role' => 'user'
            ]);

        // Создаем пользователей для отдела 3
        User::factory()->count($countPerDepartment)
            ->afterCreating(function (User $user) {
                $this->createEmploymentDetailsForUser($user);
            })
            ->create([
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

    protected function createEmploymentDetailsForUser(User $user): void
    {
        if ($this->employmentTypes->isEmpty()) {
            return;
        }

        $faker = Faker::create('ru_RU');

        $compensationType = $this->employmentTypes->firstWhere('compensation', '>', 0);
        $anotherRecipientType = $this->employmentTypes->firstWhere('is_another_recipient', true);

        if ($compensationType && !self::$compensationAssigned) {
            $employmentType = $compensationType;
            self::$compensationAssigned = true;
        } elseif ($anotherRecipientType && !self::$anotherRecipientCreated) {
            $employmentType = $anotherRecipientType;
            self::$anotherRecipientCreated = true;
        } else {
            $employmentType = $this->employmentTypes->random();
        }

        if ($employmentType->is_another_recipient) {
            $details = [
                [
                    'name' => 'first_name',
                    'value' => $faker->firstName,
                    'readName' => 'Имя',
                ],
                [
                    'name' => 'last_name',
                    'value' => $faker->lastName,
                    'readName' => 'Фамилия',
                ],
                [
                    'name' => 'surname',
                    'value' => $faker->middleName,
                    'readName' => 'Отчество',
                ],
            ];
        } else {
            $details = [];
        }

        $user->employmentDetail()->create([
            'employment_type_id' => $employmentType->id,
            'details' => $details,
            'payment_account' => '1111111111111',
        ]);
    }
}
