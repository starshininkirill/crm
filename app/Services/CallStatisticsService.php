<?php

namespace App\Services;

use App\Models\CallStat;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class CallStatisticsService
{
    protected $managersNumbers;

    public function __construct()
    {
        $department = Department::getMainSaleDepartment();
        $this->managersNumbers = $department->activeUsers()->whereNotNull('phone')->pluck('phone');
    }

    public function importData(array $callsData)
    {
        $calculatedData = $this->calculateManagerCallsData($callsData);

        $savingCount = 0;

        foreach ($calculatedData as $key => $number_data) {
            $save_result = $this->saveCallStatistic($number_data);
            if ($save_result) {
                $savingCount++;
            }
        }

        return $savingCount;
    }

    protected function saveCallStatistic(array $numberData): bool
    {
        if (empty($numberData)) {
            return false;
        }

        $record = CallStat::updateOrCreate(
            [
                'phone' => $numberData['phone'],
                'date' => $numberData['date'],
            ],
            $numberData
        );

        return $record !== null;
    }
    
    public function calculateManagerCallsData($data): array
    {
        $result = collect();
    
        foreach ($data as $dataObject) {
            // Пропускаем звонки с длительностью менее 10 секунд
            if (!isset($dataObject['conversationDuration']) || $dataObject['conversationDuration'] < 10) {
                continue;
            }
    
            $date = Carbon::parse($dataObject['date'])->format('Y-m-d');
    
            // Определяем номер менеджера (callee или caller)
            $managerNumber = null;
            if ($this->managersNumbers->contains($dataObject['calleeNumber'])) {
                $managerNumber = $dataObject['calleeNumber'];
                $callType = 'income';
            } elseif ($this->managersNumbers->contains($dataObject['callerNumber'])) {
                $managerNumber = $dataObject['callerNumber'];
                $callType = 'outcome';
            }
    
            // Если номер менеджера не найден, пропускаем запись
            if (!$managerNumber) {
                continue;
            }
    
            // Уникальный ключ для каждого менеджера и даты
            $key = "$managerNumber|$date";
    
            // Добавляем или обновляем данные в коллекции
            $existingData = $result->get($key, [
                'phone' => $managerNumber,
                'date' => $date,
                'income' => 0,
                'outcome' => 0,
                'duration' => 0,
            ]);
    
            // Обновляем значения напрямую
            $updatedData = [
                'phone' => $managerNumber,
                'date' => $date,
                'income' => $existingData['income'] + (($callType === 'income') ? 1 : 0),
                'outcome' => $existingData['outcome'] + (($callType === 'outcome') ? 1 : 0),
                'duration' => $existingData['duration'] + $dataObject['conversationDuration'],
            ];
    
            $result->put($key, $updatedData);
        }
    
        // Преобразуем коллекцию в массив результатов
        return $result->values()->all();
    }
}
