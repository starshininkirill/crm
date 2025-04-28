<?php

namespace App\Services;

use App\Classes\FileManager;
use App\Exceptions\Business\BusinessException;
use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\User;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;

class WorkStatusService
{
    public function __construct(
        private FileManager $fileManager,
        private WorkTimeService $workTimeService,
    ) {}

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

    public function handleMassUpdate(array $data)
    {
        if (empty($data)) {
            throw new BusinessException('Данные для проставления статуса не выбраны');
        }

        if (!array_key_exists('dates', $data) || count($data['dates']) < 2) {
            throw new BusinessException('Не выбраны даты для проставления статуса');
        }

        $startDate = Carbon::parse($data['dates'][0]);
        $endDate = Carbon::parse($data['dates'][1]);

        $days = DateHelper::getWorkingDaysInRange($startDate, $endDate);

        foreach ($days as $day) {
            $data = [
                'date' => $day,
                'user_id' => $data['user_id'],
                'work_status_id' => $data['work_status_id'],
                'time_start' => null,
                'time_end' => null,
            ];
            if ($currentStatus = $this->getDailyWorkStatus($day, $data['user_id'])) {
                $this->updateDailyWorkStatus($currentStatus, $data);
            } else {
                $this->createDailyWorkStatus($data);
            }
        };
    }

    public function closeSickLeave(array $data)
    {
        if (empty($data)) {
            throw new BusinessException('Данные для проставления больничного не заполнены');
        }

        if (!array_key_exists('dates', $data) || count($data['dates']) < 2) {
            throw new BusinessException('Не выбраны даты для проставления больничного');
        }

        $startDate = Carbon::parse($data['dates'][0]);
        $endDate = Carbon::parse($data['dates'][1]);
        $user =  User::find($data['user_id']);
        $path = $this->fileManager->upload($data['image'], 'sickLeave');

        $updated = $user->dailyWorkStatuses()
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->update([
                'status' => DailyWorkStatus::STATUS_APPROVED,
                'file' => $path
            ]);

        if (!$updated) {
            throw new BusinessException('Не удалось закрыть больничный');
        }
    }

    public function rejectLate($userId, $date)
    {
        $user = User::find($userId);

        $updated = $user->lateWorkStatuses()->whereDate('date', $date)->update([
            'status' => DailyWorkStatus::STATUS_REJECTED,
        ]);

        if (!$updated) {
            throw new BusinessException('Не удалось отменить опоздание');
        }
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

        $createdData = [
            'date' => $data['date'],
            'user_id' => $data['user_id'],
            'work_status_id' => $data['work_status_id'],
            'status' => $workStatus->need_confirmation ? DailyWorkStatus::STATUS_PENDING : DailyWorkStatus::STATUS_APPROVED,
            'hours' => $workStatus->hours,
            'time_start' => $data['time_start'] != null ? Carbon::parse($data['time_start']) : null,
            'time_end' => $data['time_end'] != null ? Carbon::parse($data['time_end']) : null,
        ];


        if ($workStatus->type == WorkStatus::TYPE_PART_TIME_DAY) {
            $createdData['hours'] = $this->workTimeService->hoursFromTimeCheck($createdData['time_start'], $createdData['time_end']);
        }

        $dailyWorkStatus = DailyWorkStatus::create($createdData);


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
            } else {
                $dailyWorkStatus->hours = $this->workTimeService->hoursFromTimeCheck(Carbon::parse($data['time_start']), Carbon::parse($data['time_end']));
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
