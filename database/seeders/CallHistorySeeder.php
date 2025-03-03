<?php

namespace Database\Seeders;

use App\Models\CallHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CallHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Получаем путь к файлу JSON
        $file_path = 'exports/call_stats.json';

        // Проверяем, существует ли файл
        if (!Storage::exists($file_path)) {
            return;
        }

        // Читаем содержимое файла
        $jsonData = Storage::get($file_path);

        // Преобразуем JSON-строку в массив
        $CallHistorysData = json_decode($jsonData, true);

        // Заполняем таблицу данными
        foreach ($CallHistorysData as $data) {
            if (User::query()->firstWhere('phone', $data['phone'])) {
                CallHistory::create($data);
            }
        }

        $this->command->info('Данные успешно импортированы из файла JSON.');
    }
}
