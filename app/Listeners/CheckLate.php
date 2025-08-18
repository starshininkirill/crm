<?php

namespace App\Listeners;

use App\Events\StartWorkDay;
use App\Helpers\DateHelper;
use App\Models\TimeTracking\DailyWorkStatus;
use App\Models\TimeTracking\TimeCheck;
use App\Models\TimeTracking\WorkStatus;
use Illuminate\Support\Facades\Log;

class CheckLate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StartWorkDay $event): void
    {
        if(!DateHelper::isWorkingDay($event->action->date)){
            return;
        }

        $dateStartTime = $event->action->date->format('H:i:s');

        if ($dateStartTime <= TimeCheck::DEFAULT_DAY_START) {
            return;
        }

        $workStatus = $event->action->user->dailyWorkStatuses()
            ->with('workStatus')
            ->whereDate('date', $event->action->date)
            ->whereHas('workStatus', function ($query) {
                $query->where('type', WorkStatus::TYPE_PART_TIME_DAY);
            })
            ->first();

        if ($workStatus && $dateStartTime < $workStatus->time_start) {
            return;
        }

        $lateWorkStatus = WorkStatus::lateStatuses()->first();
        if (!$lateWorkStatus) {
            Log::warning('Не найден статус для опоздания (WorkStatus)');
            return;
        }

        $currentDate = $event->action->date;
        $user = $event->action->user;

        $latesCountThisMonth = DailyWorkStatus::where('user_id', $user->id)
            ->whereIn('work_status_id', WorkStatus::lateStatuses()->pluck('id'))
            ->whereYear('date', $currentDate->year)
            ->whereMonth('date', $currentDate->month)
            ->whereDate('date', '<', $currentDate->toDateString())
            ->count();

        $penalty = $latesCountThisMonth === 0 ? 0 : DailyWorkStatus::LATE_PENALTY;

        DailyWorkStatus::updateOrCreate(
            [
                'date' => $currentDate->format('Y-m-d'),
                'user_id' => $user->id,
                'work_status_id' => $lateWorkStatus->id,
            ],
            [
                'status' => DailyWorkStatus::STATUS_APPROVED,
                'time_start' => $dateStartTime,
                'money' => $penalty,
            ]
        );
    }
}
