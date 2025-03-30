<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Models\DailyWorkStatus;
use App\Models\WorkStatus;
use Carbon\Carbon;

class WorkStatusService
{
    public function handleChange(array $data)
    {
        $existingDailyWorkStatus = $this->getDailyWorkStatus($data['date'], $data['user_id']);

        if ($data['work_status_id'] == null && $existingDailyWorkStatus) {
            $this->deleteDailyWorkStatus($existingDailyWorkStatus);
            return;
        }

        if ($data['work_status_id'] == null && !$existingDailyWorkStatus) {
            return;
        }

        if (!$existingDailyWorkStatus) {
            $this->createDailyWorkStatus($data);
            return;
        }

        $this->updateDailyWorkStatus($existingDailyWorkStatus, $data);
    }

    private function deleteDailyWorkStatus($existingDailyWorkStatus)
    {
        return $existingDailyWorkStatus->delete();
    }

    private function createDailyWorkStatus($data)
    {
        $workStatus = WorkStatus::find($data['work_status_id']);

        if (!$workStatus) {
            throw new BusinessException('Такого статуса не существует');
        };

        $dailyWorkStatus = DailyWorkStatus::create([
            'date' => $data['date'],
            'user_id' => $data['user_id'],
            'work_status_id' => $data['work_status_id'],
            'status' => $workStatus->need_confirmation ? DailyWorkStatus::STATUS_PENDING : DailyWorkStatus::STATUS_APPROVED,
            'hours' => $workStatus->hours,
            'time_start' => $data['time_start'] != null ? Carbon::parse($data['time_start']) : null,
            'time_end' => $data['time_end'] != null ? Carbon::parse($data['time_end']) : null,
        ]);


        if (!$dailyWorkStatus) {
            throw new BusinessException('Не удалось обновить статус');
        }

        return $dailyWorkStatus;
    }

    private function updateDailyWorkStatus(DailyWorkStatus $dailyWorkStatus, array $data)
    {
        if (array_key_exists('work_status_id', $data)) {
            $workStatus = WorkStatus::find($data['work_status_id']);

            if (!$workStatus) {
                throw new BusinessException('Такого статуса не существует');
            };

            $dailyWorkStatus->work_status_id = $data['work_status_id'];
            $dailyWorkStatus->time_start = $data['time_start'] ?? null;
            $dailyWorkStatus->time_end = $data['time_end'] ?? null;
            $dailyWorkStatus->status = $workStatus->need_confirmation ? DailyWorkStatus::STATUS_PENDING : DailyWorkStatus::STATUS_APPROVED;
            $dailyWorkStatus->hours = $workStatus->hours;

            if ($workStatus->type != WorkStatus::TYPE_PART_TIME_DAY) {
                $dailyWorkStatus->time_start = null;
                $dailyWorkStatus->time_end = null;
            }
        }

        if (!$dailyWorkStatus->save()) {
            throw new BusinessException('Не удалось обновить статус');
        }
    }

    private function getDailyWorkStatus($date, $userId)
    {
        return DailyWorkStatus::query()->whereDate('date', $date)
            ->where('user_id', $userId)
            ->whereHas('workStatus', function ($query) {
                $query->whereNotIn('type', WorkStatus::EXCLUDE_TYPES);
            })
            ->first();
    }
}
