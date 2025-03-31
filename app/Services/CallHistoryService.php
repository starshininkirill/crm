<?php

namespace App\Services;

use App\Models\CallHistory;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CallHistoryService
{
    protected $managersNumbers;

    public function __construct()
    {
        $department = Department::getMainSaleDepartment();
        $this->managersNumbers = $department->users()->whereNotNull('phone')->pluck('phone');
    }

    public function calculateTotalCallsData($date)
    {
        $preparedDate = $this->prepareDatesForQuery($date);

        $calls = CallHistory::with('user')->whereBetween('date', [$preparedDate['dateStart'], $preparedDate['dateEnd']])->get();

        $callsData = $this->calculateManagerCallsData($calls);

        if ($callsData->isEmpty()) {
            return [
                'error' => 'Нет данных для расчёта'
            ];
        }

        $callsDataByDate = $this->initializeCallsDataByDate($preparedDate['daysInMonth']);
        $totalNumberValues = [];

        foreach ($callsData as $numberStat) {
            $actionDay = Carbon::create($numberStat['date'])->format('j');
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

    public function importData(array $callsData)
    {
        $filtredCalls = $this->filterCallsData($callsData);

        $savingCount = 0;

        foreach ($filtredCalls as $key => $callData) {
            $save_result = $this->saveCall($callData);
            if ($save_result) {
                $savingCount++;
            }
        }

        return $savingCount;
    }

    public function calculateManagerCallsData($calls): Collection
    {
        $result = collect();

        foreach ($calls as $call) {

            $date = Carbon::parse($call['date'])->format('Y-m-d');

            $key = "$call->phone|$date";

            $existingData = $result->get($key, [
                'phone' => $call->phone,
                'date' => $date,
                'income' => 0,
                'outcome' => 0,
                'duration' => 0,
            ]);

            $updatedData = [
                'phone' => $call->phone,
                'date' => $date,
                'income' => $existingData['income'] + (($call->type === 'income') ? 1 : 0),
                'outcome' => $existingData['outcome'] + (($call->type === 'outcome') ? 1 : 0),
                'duration' => $existingData['duration'] + $call->conversation_duration,
                'user' => $call->user,
            ];

            $result->put($key, $updatedData);
        }

        return $result;
    }

    private function filterCallsData(array $callsData): Collection
    {
        $filtredCallsData = collect();

        foreach ($callsData as $call) {
            if (!isset($call['conversationDuration']) || $call['conversationDuration'] < 10) {
                continue;
            }

            $managerNumber = null;
            if ($this->managersNumbers->contains($call['calleeNumber'])) {
                $managerNumber = $call['calleeNumber'];
                $cliendNumber = $call['callerNumber'];
                $callType = 'income';
            } elseif ($this->managersNumbers->contains($call['callerNumber'])) {
                $managerNumber = $call['callerNumber'];
                $cliendNumber = $call['calleeNumber'];
                $callType = 'outcome';
            }

            if (!$managerNumber) {
                continue;
            }

            $filtredCallsData[] = [
                'date' => $call['date'],
                'phone' => $managerNumber,
                'client_phone' => $cliendNumber,
                'conversation_duration' => $call['conversationDuration'],
                'type' => $callType,
            ];
        }
        return $filtredCallsData;
    }

    private function saveCall(array $callData): bool
    {
        if (empty($callData)) {
            return false;
        }

        $record = CallHistory::updateOrCreate(
            [
                'phone' => $callData['phone'],
                'date' => Carbon::parse($callData['date'])->format('Y-m-d'),
                'client_phone' => $callData['client_phone'],
                'type' => $callData['type'],
                'conversation_duration' => $callData['conversation_duration'],
            ],
            $callData
        );

        return $record != null;
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

        if (!isset($callsDataByDate[$actionDay][$numberStat['phone']])) {
            $callsDataByDate[$actionDay][$numberStat['phone']] = [
                'calls' => 0,
                'duration' => 0,
            ];
        }

        $callsDataByDate[$actionDay][$numberStat['phone']] = [
            'calls' => $numberStat['income'] + $numberStat['outcome'],
            'duration' => round($numberStat['duration'] / 60, 1),
        ];
    }

    private function updateTotalNumberValues(&$totalNumberValues, $callsDataByDate, $numberStat, $actionDay)
    {
        if (!isset($totalNumberValues[$numberStat['phone']])) {
            $totalNumberValues[$numberStat['phone']] = [
                'total_calls' => 0,
                'total_duration' => 0,
                'active_days' => 0,
                'middle_value' => 0,
                'number' => $numberStat['phone'],
                'user' => $numberStat['user'],
            ];
        }

        $totalNumberValues[$numberStat['phone']]['total_calls'] += $callsDataByDate[$actionDay][$numberStat['phone']]['calls'];
        $totalNumberValues[$numberStat['phone']]['total_duration'] += $callsDataByDate[$actionDay][$numberStat['phone']]['duration'];
        // TODO
        // Переписать когда сделаю work time

        // if ($callsDataByDate[$actionDay][$numberStat['phone']]['duration']) {
        //     if (array_key_exists($numberStat['phone'], $user_ids_by_number)) {

        //         // ПРОВЕРЯЕМ НА ПОЛНОЦЕННОСТЬ РАБОЧЕГО ДНЯ ИЛИ ЕГО ПОЛОВИНКУ
        //         $user_worktime = GTC()->actions->get_user_worktime($user_ids_by_number[$numberStat['phone']], $numberStat['date']);

        //         if ($user_worktime == 0) {
        //             continue;
        //         }

        //         if ($user_worktime > 14400) {
        //             $totalNumberValues[$numberStat['phone']]['active_days']++;
        //         } else {
        //             $totalNumberValues[$numberStat['phone']]['active_days'] += 0.5;
        //         }
        //     } else {
        //         $totalNumberValues[$numberStat['phone']]['active_days']++;
        //     }
        // }
        $totalNumberValues[$numberStat['phone']]['active_days']++;
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

    private function prepareDatesForQuery($targetDate)
    {
        $targetMonth = date('m', strtotime($targetDate));
        $targetYear = date('Y', strtotime($targetDate));

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $targetMonth, $targetYear);

        $dateStart = $targetYear . '-' . $targetMonth . '-01';
        $dateEnd = $targetYear . '-' . $targetMonth . '-' . $daysInMonth;

        return compact('daysInMonth', 'dateStart', 'dateEnd', 'targetMonth', 'targetYear');
    }
}
