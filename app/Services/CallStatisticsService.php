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

    public function calculateTotalCallsData($date)
    {
        $preparedDate = $this->prepareDatesForQuery($date);

        $callsData = CallStat::whereBetween('created_at', [$preparedDate['dateStart'], $preparedDate['dateEnd']])->get();

        if ($callsData->isEmpty()) {
            return [
                'error' => 'Нет данных для расчёта'
            ];
        }

        $callsDataByDate = $this->initializeCallsDataByDate($preparedDate['daysInMonth']);
        $totalNumberValues = [];

        foreach ($callsData as $numberStat) {
            $actionDay = Carbon::create($numberStat->date)->format('j');
            $this->updateCallsDataByDate($callsDataByDate, $numberStat, $actionDay);
            $this->updateTotalNumberValues($totalNumberValues, $callsDataByDate, $numberStat, $actionDay);
        }

        $this->calculateMiddleValues($totalNumberValues);

        $sortedTotalNumberValues = $this->sortTotalNumberValues($totalNumberValues);

        return [
            'callsDataByDate' => $callsDataByDate,
            'totalNumberValues' => $sortedTotalNumberValues,
        ];
    }

    private function initializeCallsDataByDate($daysInMonth)
    {
        return array_fill(1, $daysInMonth, []);
    }

    private function updateCallsDataByDate(&$callsDataByDate, $numberStat, $actionDay)
    {
        if (!isset($callsDataByDate[$actionDay])) {
            $callsDataByDate[$actionDay] = [];
        }

        if (!isset($callsDataByDate[$actionDay][$numberStat->phone])) {
            $callsDataByDate[$actionDay][$numberStat->phone] = [
                'calls' => 0,
                'duration' => 0,
            ];
        }

        $callsDataByDate[$actionDay][$numberStat->phone] = [
            'calls' => $numberStat->income + $numberStat->outcome,
            'duration' => round($numberStat->duration / 60, 1), 
        ];
    }

    private function updateTotalNumberValues(&$totalNumberValues, $callsDataByDate, $numberStat, $actionDay)
    {
        if (!isset($totalNumberValues[$numberStat->phone])) {
            $totalNumberValues[$numberStat->phone] = [
                'total_calls' => 0,
                'total_duration' => 0,
                'active_days' => 0,
                'middle_value' => 0,
                'number' => $numberStat->phone,
                'user' => $numberStat->user,
            ];
        }

        $totalNumberValues[$numberStat->phone]['total_calls'] += $callsDataByDate[$actionDay][$numberStat->phone]['calls'];
        $totalNumberValues[$numberStat->phone]['total_duration'] += $callsDataByDate[$actionDay][$numberStat->phone]['duration'];
        // TODO
        // Переписать когда сделаю work time

        // if ($callsDataByDate[$actionDay][$numberStat->phone]['duration']) {
        //     if (array_key_exists($numberStat->phone, $user_ids_by_number)) {

        //         // ПРОВЕРЯЕМ НА ПОЛНОЦЕННОСТЬ РАБОЧЕГО ДНЯ ИЛИ ЕГО ПОЛОВИНКУ
        //         $user_worktime = GTC()->actions->get_user_worktime($user_ids_by_number[$numberStat->phone], $numberStat['date']);

        //         if ($user_worktime == 0) {
        //             continue;
        //         }

        //         if ($user_worktime > 14400) {
        //             $totalNumberValues[$numberStat->phone]['active_days']++;
        //         } else {
        //             $totalNumberValues[$numberStat->phone]['active_days'] += 0.5;
        //         }
        //     } else {
        //         $totalNumberValues[$numberStat->phone]['active_days']++;
        //     }
        // }
        $totalNumberValues[$numberStat->phone]['active_days']++;
    }

    private function calculateMiddleValues(&$totalNumberValues)
    {
        foreach ($totalNumberValues as $number => $totalData) {
            if ($totalData['total_calls'] == 0 || $totalData['active_days'] == 0) {
                continue;
            }

            $totalDurationInMin = $totalData['total_duration'];
            $totalNumberValues[$number]['middle_value'] = round($totalDurationInMin / $totalData['active_days'], 1);
            $totalNumberValues[$number]['middle_calls'] = round($totalData['total_calls'] / $totalData['active_days'], 1);
        }
    }

    private function sortTotalNumberValues($totalNumberValues)
    {
        uasort($totalNumberValues, function ($a, $b) {
            return $b['middle_value'] <=> $a['middle_value'];
        });

        return $totalNumberValues;
    }

    // public function calculateTotalCallsData($preparedDate)
    // {
    //     $callsData = CallStat::whereBetween('created_at', [$preparedDate['dateStart'], $preparedDate['dateEnd']])->get();

    //     if (!$callsData) {
    //         return [
    //             'error' => 'Нет данных для расчёта'
    //         ];
    //     }

    //     // ПОДГОТОВКА ПЕРЕМЕННЫХ
    //     $callsDataByDate = array_fill(1, $preparedDate['daysInMonth'], []);
    //     $totalNumberValues = [];

    //     foreach ($callsData as $numberStat) {

    //         $actionDay = $numberStat->created_at->format('j');;
    //         if (!array_key_exists($actionDay, $callsDataByDate)) {
    //             $callsDataByDate[$actionDay] = [];
    //         }

    //         if (!array_key_exists($numberStat->phone, $callsDataByDate[$actionDay])) {
    //             $callsDataByDate[$actionDay][$numberStat->phone] = [
    //                 'total_calls' => 0,
    //                 'duration' => 0,
    //             ];
    //         }
    //         // НАПОЛНЯЕМ МАССИВ ДАННЫХ ЗА ДЕНЬ
    //         $callsDataByDate[$actionDay][$numberStat->phone] = [
    //             'calls' => $numberStat->income + $numberStat->outcome,
    //             'duration' => $numberStat->duration,
    //         ];


    //         // НАОПЛНЯЕМ МАССИВ ТОТАЛОМ ДАННЫХ
    //         if (!array_key_exists($numberStat->phone, $totalNumberValues)) {
    //             $totalNumberValues[$numberStat->phone] = [
    //                 'total_calls' => 0,
    //                 'total_duration' => 0,
    //                 'active_days' => 0,
    //                 'middle_value' => 0,
    //                 'number' => $numberStat->phone,
    //                 'user' => $numberStat->user,
    //             ];
    //         }

    //         $totalNumberValues[$numberStat->phone]['total_calls'] += $callsDataByDate[$actionDay][$numberStat->phone]['calls'];
    //         $totalNumberValues[$numberStat->phone]['total_duration'] += $callsDataByDate[$actionDay][$numberStat->phone]['duration'];


    //         // TODO
    //         // Переписать когда сделаю work time

    //         // if ($callsDataByDate[$actionDay][$numberStat->phone]['duration']) {
    //         //     if (array_key_exists($numberStat->phone, $user_ids_by_number)) {

    //         //         // ПРОВЕРЯЕМ НА ПОЛНОЦЕННОСТЬ РАБОЧЕГО ДНЯ ИЛИ ЕГО ПОЛОВИНКУ
    //         //         $user_worktime = GTC()->actions->get_user_worktime($user_ids_by_number[$numberStat->phone], $numberStat['date']);

    //         //         if ($user_worktime == 0) {
    //         //             continue;
    //         //         }

    //         //         if ($user_worktime > 14400) {
    //         //             $totalNumberValues[$numberStat->phone]['active_days']++;
    //         //         } else {
    //         //             $totalNumberValues[$numberStat->phone]['active_days'] += 0.5;
    //         //         }
    //         //     } else {
    //         //         $totalNumberValues[$numberStat->phone]['active_days']++;
    //         //     }
    //         // }
    //         $totalNumberValues[$numberStat->phone]['active_days']++;
    //     }


    //     foreach ($totalNumberValues as $number => $totalData) {
    //         if ($totalData['total_calls'] == 0) {
    //             continue;
    //         }
    //         if ($totalData['active_days'] == 0) {
    //             continue;
    //         }
    //         $total_duration_in_min = round($totalData['total_duration'] / 60, 1);

    //         $totalNumberValues[$number]['middle_value'] = round($total_duration_in_min / $totalData['active_days'], 1);
    //     }


    //     // СОРТИРОВКА ОТ БОЛЬШЕГО К МЕНЬШЕМУ 
    //     uasort($totalNumberValues, function ($a, $b) {
    //         if ($a['middle_value'] == $b['middle_value']) {
    //             return 0;
    //         }
    //         return ($a['middle_value'] > $b['middle_value']) ? -1 : 1;
    //     });

    //     return [
    //         'callsDataByDate' => $callsDataByDate,
    //         'totalNumberValues' => $totalNumberValues,
    //     ];
    // }


    public function prepareDatesForQuery($targetDate)
    {
        $targetMonth = date('m', strtotime($targetDate));
        $targetYear = date('Y', strtotime($targetDate));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $targetMonth, $targetYear);

        $dateStart = $targetYear . '-' . $targetMonth . '-01';
        $dateEnd = $targetYear . '-' . $targetMonth . '-' . $daysInMonth;

        return compact('daysInMonth', 'dateStart', 'dateEnd', 'targetMonth', 'targetYear');
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
