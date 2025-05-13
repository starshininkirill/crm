<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\User;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class TimeSheetService
{
    public function __construct(
        private WorkTimeService $service
    ) {}

    public function generateUsersReport(Collection $users, Carbon $date)
    {
        $this->loadRelation($users, $date);

        $usersReport = $users->map(function ($user) use ($date) {
            $user['days'] = $this->userMonthReport($user, $date);
            $user['salary'] = $user->getSalary();
            $user['hour_salary'] = round($user['salary'] / count(DateHelper::getWorkingDaysInMonth($date)) / 9);
            return $user;
        });

        return $usersReport;
    }
    public function userMonthReport(User $user, Carbon $date)
    {
        $days = DateHelper::daysInMonth($date);

        $this->loadRelation($user, $date);

        $days = $days->map(function ($day) use ($user) {
            return $this->userDayReport($user, $day);
        });

        return $days;
    }

    private function userDayReport(User $user, $day): array
    {
        $workingDayInfo = $this->service->countWorkHours($user, Carbon::parse($day['date']));
        $day['hours'] = $workingDayInfo['totalHours'];
        $day['timeCheckHours'] = $workingDayInfo['timeCheckHours'];
        $day['status'] = $workingDayInfo['status'];

        $day['statuses'] = $user->dailyWorkStatuses->filter(function ($status) use ($day) {
            if (in_array($status->workStatus->type, WorkStatus::EXCLUDE_TYPES)) {
                if ($status->status != DailyWorkStatus::STATUS_APPROVED) {
                    return false;
                }
            }
            if($status->workStatus->type == WorkStatus::TYPE_PART_TIME_DAY){
                if ($status->hours > $day['timeCheckHours'] && $day['timeCheckHours'] != null) {
                    return false;
                }
            }
            return $status->date->isSameDay($day['date']);
        })->values();

        $day['isLate'] = $day['statuses']->contains(function ($status) use ($day) {
            return $status->workStatus->type == WorkStatus::TYPE_LATE;
        });

        $day['isWorkingDay'] = DateHelper::isWorkingDay($day['date']);

        return $day;
    }

    private function loadRelation($users, Carbon $date): bool
    {
        $needsLoad = false;

        if ($users instanceof User) {
            $needsLoad = !$users->relationLoaded('dailyWorkStatuses');
            $users = collect([$users]);
        } elseif ($users instanceof EloquentCollection) {
            $needsLoad = $users->contains(function ($user) {
                return !$user->relationLoaded('dailyWorkStatuses');
            });
        } else {
            return false;
        }

        if ($needsLoad) {
            $dateStart = $date->copy()->startOfMonth();
            $dateEnd = $date->copy()->endOfMonth();

            $users->load(['dailyWorkStatuses' => function ($query) use ($dateStart, $dateEnd) {
                $query->whereBetween('date', [$dateStart, $dateEnd])
                    ->with(['workStatus']);
            }]);

            return true;
        }

        return false;
    }
}
