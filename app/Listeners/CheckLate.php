<?php

namespace App\Listeners;

use App\Events\StartWorkDay;
use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\TimeCheck;
use App\Models\WorkStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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

        $newStatus = DailyWorkStatus::updateOrCreate(
            [
                'date' => $event->action->date->format('Y-m-d'),
                'user_id' => $event->action->user->id,
                'work_status_id' => WorkStatus::lateStatuses()->first()?->id,
            ],
            [
                'status' => DailyWorkStatus::STATUS_APPROVED,
                'time_start' => $dateStartTime,
            ]
        );
    }
}
