<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\User;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class TimeSheetService
{
    public function __construct(
        private WorkTimeService $service
    ){}

    public function generateUsersReport(Collection $users, Carbon $date)
    {
        $this->loadRelation($users, $date);

        $usersReport = $users->map(function ($user) use ($date) {
            $user['salary'] = $user->salary();
            $user['days'] = $this->userMonthReport($user, $date);
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
        $day['status'] = $user->dailyWorkStatuses->first(function ($status) use ($day) {
            return $status->date->isSameDay($day['date']);
        });

        $day['isWorkingDay'] = DateHelper::isWorkingDay($day['date']);
        $day['hours'] = $this->service->countWorkHours($user, Carbon::parse($day['date']));

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
