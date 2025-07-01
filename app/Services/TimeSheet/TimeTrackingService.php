<?php
namespace App\Services\TimeSheet;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\User;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class TimeTrackingService
{
    public function __construct(private WorkTimeService $workTimeService)
    {
    }

    public function userMonthReport(User $user, Carbon $date): Collection
    {
        $days = DateHelper::daysInMonth($date);

        return $days->map(function ($day) use ($user) {
            return $this->userDayReport($user, $day);
        });
    }

    private function userDayReport(User $user, array $day): array
    {
        $workingDayInfo = $this->workTimeService->countWorkHours($user, Carbon::parse($day['date']));
        $day['hours'] = $workingDayInfo['totalHours'];
        $day['timeCheckHours'] = $workingDayInfo['timeCheckHours'];
        $day['status'] = $workingDayInfo['status'];

        $day['statuses'] = $user->dailyWorkStatuses->filter(function ($status) use ($day) {
            if (in_array($status->workStatus->type, WorkStatus::EXCLUDE_TYPES)) {
                if ($status->status != DailyWorkStatus::STATUS_APPROVED) {
                    return false;
                }
            }
            if ($status->workStatus->type == WorkStatus::TYPE_PART_TIME_DAY) {
                if ($status->hours > $day['timeCheckHours'] && $day['timeCheckHours'] != null) {
                    return false;
                }
            }
            return $status->date->isSameDay($day['date']);
        })->values();

        $day['isLate'] = $day['statuses']->contains(function ($status) {
            return $status->workStatus->type == WorkStatus::TYPE_LATE;
        });

        $day['isWorkingDay'] = DateHelper::isWorkingDay($day['date']);

        return $day;
    }

    public function calculateTimeData(Collection $daysReport, Collection $workingDays): array
    {
        $firstHalfAmountHours = $workingDays->filter(fn($date) => Carbon::parse($date)->day <= 14)->count() * 9;
        $secondHalfAmountHours = $workingDays->filter(fn($date) => Carbon::parse($date)->day > 14)->count() * 9;

        $firstHalfUserWorkingHours = $daysReport->filter(fn($day) => $day['date']->day <= 14)->sum('hours');
        $secondHalfUserWorkingHours = $daysReport->filter(fn($day) => $day['date']->day > 14)->sum('hours');

        return [
            'first_half_hours' => $firstHalfUserWorkingHours - $firstHalfAmountHours,
            'second_half_hours' => $secondHalfUserWorkingHours - $secondHalfAmountHours,
            'first_half_user_working_hours' => $firstHalfUserWorkingHours,
            'second_half_user_working_hours' => $secondHalfUserWorkingHours,
        ];
    }
} 