<?php

namespace App\Services\TimeCheckServices;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\TimeCheck;
use App\Models\User;
use App\Models\WorkStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkTimeService
{

    private $startTime;
    private $maxBrektime;

    public function countWorkHours(User $user, Carbon $date): float|int|string
    {
        $statusesForDay = $user->dailyWorkStatuses->filter(function ($dailyWorkStatus) use ($date) {
            return $dailyWorkStatus->date->isSameDay($date) &&
                !in_array($dailyWorkStatus->workStatus?->type, WorkStatus::EXCLUDE_TYPES);
        });

        $timeChecksForDay = $user->timeChecks->filter(function ($timeCheck) use ($date) {
            return $timeCheck->date->format('Y-m-d') ==  $date->format('Y-m-d');
        });

        $overworkHours = $user->dailyWorkStatuses->filter(function ($dailyWorkStatus) use ($date) {
            return $dailyWorkStatus->date->isSameDay($date)
                && $dailyWorkStatus->status == DailyWorkStatus::STATUS_APPROVED
                && in_array($dailyWorkStatus->workStatus?->type, [WorkStatus::TYPE_OVERWORK]);
        })->sum('hours');


        if (($confirmedHours = $this->hoursFromStatus($statusesForDay)) !== null) {
            return $confirmedHours + $overworkHours;
        }

        if (!DateHelper::isWorkingDay($date)) {
            return 0 + $overworkHours;
        }

        $startTime = $timeChecksForDay->firstWhere('action', TimeCheck::ACTION_START)?->date;

        if (!$startTime) {
            return 0 + $overworkHours;
        }

        $endTime = $timeChecksForDay->firstWhere('action', TimeCheck::ACTION_END)?->date ?? Carbon::now();

        if (!$startTime || !$endTime) {
            return 0 + $overworkHours;
        }

        if ($startTime->format('H:i:s') <= TimeCheck::DEFAULT_DAY_START && $endTime->format('H:i:s') >= TimeCheck::DEFAULT_DAY_END) {
            return TimeCheck::DEFAULT_WORKING_DAY_DURATION + $overworkHours;
        }

        return $this->hoursFromTimeCheck($startTime, $endTime) + $overworkHours;
    }

    public function isUserLate(User $user, $date): bool
    {
        $date = Carbon::parse($date);

        if (!$user->lateWorkStatuses->isEmpty()) {
            return true;
        }

        return false;
    }

    public function isBreakOvertime(int $breaktime): bool
    {
        if (!$this->maxBrektime) {
            $this->maxBrektime = TimeCheck::DEAFULT_BREAKTIME;
        }

        $maxBrektime = Carbon::createFromFormat('H:i:s', $this->maxBrektime);

        $maxBrektimeCarbon = Carbon::createFromFormat('H:i:s', $this->maxBrektime);

        $maxBrektimeInSeconds = $maxBrektimeCarbon->hour * 3600
            + $maxBrektimeCarbon->minute * 60
            + $maxBrektimeCarbon->second;


        if ($breaktime > $maxBrektimeInSeconds) {
            return true;
        }

        return false;
    }

    public function userWorktime(TimeCheck|null $actionStart, TimeCheck|null $actionEnd, int $breaktime): int
    {
        $workTime = 0;
        if ($actionStart && $actionEnd) {
            $startTime = Carbon::parse($actionStart->date);
            $endTime = Carbon::parse($actionEnd->date);
            $workTime = $startTime->diffInSeconds($endTime) - $breaktime;
        }

        if ($actionStart && !$actionEnd) {
            $startTime = Carbon::parse($actionStart->date);
            $endTime = Carbon::now();
            $workTime = $startTime->diffInSeconds($endTime) - $breaktime;
        }

        return $workTime;
    }

    public function userBreaktime(User $user, Carbon $date = null): int
    {
        if (!$date) {
            $date = Carbon::now();
        }

        $breaktimes = $this->getBreakTimeChecks($user, $date);

        $totalBreakTime = 0;
        $pauseStart = null;

        foreach ($breaktimes as $timeCheck) {
            if ($timeCheck->action === TimeCheck::ACTION_PAUSE) {
                $pauseStart = Carbon::parse($timeCheck->date);
            } elseif ($timeCheck->action === TimeCheck::ACTION_CONTINUE && $pauseStart) {
                $continueTime = Carbon::parse($timeCheck->date);
                $totalBreakTime += $pauseStart->diffInSeconds($continueTime);
                $pauseStart = null;
            }
        }

        if ($pauseStart) {
            $totalBreakTime += $pauseStart->diffInSeconds(Carbon::now());
        }

        return $totalBreakTime;
    }

    private function hoursFromStatus(Collection $statusesForDay): ?float
    {
        if ($statusesForDay->isNotEmpty()) {
            $status = $statusesForDay->first();

            if ($status->hours) {
                if ($status->status == DailyWorkStatus::STATUS_APPROVED) {
                    return $status->hours;
                } else {
                    return 0;
                }
            }
        }

        return null;
    }

    private function hoursFromTimeCheck(Carbon $startTime, Carbon $endTime): float
    {
        $adjustedStartTime = $this->adjustTimeToNearestInterval($startTime);
        $adjustedEndTime = $this->adjustTimeToNearestInterval($endTime);

        $hours = $adjustedStartTime->diffInHours($adjustedEndTime);

        return min($hours, TimeCheck::DEFAULT_WORKING_DAY_DURATION);
    }


    private function adjustTimeToNearestInterval(Carbon $time): Carbon
    {
        $minutes = $time->minute;

        if ($minutes >= 0 && $minutes < 15) {
            // Сдвигаем до 0 минут
            return $time->copy()->setTime($time->hour, 0);
        } elseif ($minutes >= 15 && $minutes < 45) {
            // Сдвигаем до 30 минут
            return $time->copy()->setTime($time->hour, 30);
        } else {
            // Сдвигаем до следующего часа (0 минут)
            return $time->copy()->addHour()->setTime($time->hour + 1, 0);
        }
    }

    private function getBreakTimeChecks(User $user, Carbon $date): Collection
    {
        if ($user->relationLoaded('timeChecks')) {
            return $user->timeChecks
                ->whereIn('action', [TimeCheck::ACTION_CONTINUE, TimeCheck::ACTION_PAUSE])
                ->filter(fn($timeCheck) => $timeCheck->date->isSameDay($date))
                ->sortBy('date');
        } else {
            return $user->timeChecks()
                ->whereIn('action', [TimeCheck::ACTION_CONTINUE, TimeCheck::ACTION_PAUSE])
                ->whereDate('date', $date)
                ->orderBy('date')
                ->get();
        }
    }
}
