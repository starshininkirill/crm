<?php

namespace Database\Seeders;

use App\Models\WorkStatus;
use Illuminate\Database\Seeder;

class WorkStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WorkStatus::create([
            'name' => 'Больничный',
            'type' => WorkStatus::TYPE_SICK_LEAVE,
            'hours' => 4.5,
            'need_confirmation' => true,
        ]);

        WorkStatus::create([
            'name' => 'День за свой счет',
            'type' => WorkStatus::TYPE_OWN_DAY,
            'hours' => 0
        ]);

        WorkStatus::create([
            'name' => 'Отпуск',
            'type' => WorkStatus::TYPE_VACATION,
            'hours' => 9
        ]);

        WorkStatus::create([
            'name' => 'Работа из дома',
            'type' => WorkStatus::TYPE_HOMEWORK,
            'hours' => 9,
            'need_confirmation' => true,
        ]);

        WorkStatus::create([
            'name' => 'Неполный день',
            'hours' => null,
            'type' => WorkStatus::TYPE_PART_TIME_DAY,
        ]);

        WorkStatus::create([
            'name' => 'Опоздание',
            'hours' => null,
            'type' => WorkStatus::TYPE_LATE,
        ]);

        WorkStatus::create([
            'name' => 'Переработка',
            'hours' => null,
            'type' => WorkStatus::TYPE_OVERWORK,
        ]);
    }
}
