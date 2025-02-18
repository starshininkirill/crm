<?php

namespace Database\Seeders;

use App\Models\CallStat;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CallStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Получаем путь к файлу JSON
        $file_path = 'public/call_stats.json';

        // Проверяем, существует ли файл
        if (!Storage::exists($file_path)) {
            return;
        }

        // Читаем содержимое файла
        $jsonData = Storage::get($file_path);

        // Преобразуем JSON-строку в массив
        $callStatsData = json_decode($jsonData, true);

        // Заполняем таблицу данными
        foreach ($callStatsData as $data) {
            if (User::query()->firstWhere('phone', $data['phone'])) {
                CallStat::create($data);
            }
        }

        $this->command->info('Данные успешно импортированы из файла JSON.');
    }
}
