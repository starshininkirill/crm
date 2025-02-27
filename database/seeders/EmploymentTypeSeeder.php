<?php

namespace Database\Seeders;

use App\Models\EmploymentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Получаем путь к файлу JSON
        $file_path = 'exports/employmentType.json';

        // Проверяем, существует ли файл
        if (!Storage::exists($file_path)) {
            return;
        }

        // Читаем содержимое файла
        $jsonData = Storage::get($file_path);

        // Преобразуем JSON-строку в массив
        $employmentTypeData = json_decode($jsonData, true);

        // Заполняем таблицу данными
        foreach ($employmentTypeData as $data) {
            EmploymentType::create($data);
        }

        $this->command->info('Данные успешно импортированы из файла JSON.');
    }
}
