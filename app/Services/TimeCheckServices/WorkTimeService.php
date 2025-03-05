<?php

namespace App\Services\TimeCheckServices;

use App\Models\Option;
use App\Models\TimeCheck;
use App\Models\User; 
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkTimeService
{

    private $startTime;
    private $maxBrektime;

    public function isUserLate(TimeCheck|null $actionStart): bool
    {
        if(!$actionStart){
            return false;
        }
        
        if(!$this->startTime){
            $this->startTime = Option::whereName('time_check_start_work_day_time')->first()->value ?? '09:01:00';
        }

        $startTime = Carbon::createFromFormat('H:i:s', $this->startTime);

        if ($actionStart->date->gt($startTime)) {
            return true;
        }
        
        return false;
    }

    public function isBreakOvertime(int $breaktime): bool
    {
        if(!$this->maxBrektime){
            $this->maxBrektime = Option::whereName('time_check_max_breaktime')->first()->value ?? '01:21:00';
        }

        $maxBrektime = Carbon::createFromFormat('H:i:s', $this->maxBrektime);

        $maxBrektimeCarbon = Carbon::createFromFormat('H:i:s', $this->maxBrektime);

        $maxBrektimeInSeconds = $maxBrektimeCarbon->hour * 3600 
            + $maxBrektimeCarbon->minute * 60 
            + $maxBrektimeCarbon->second;
    

        if($breaktime > $maxBrektimeInSeconds){
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
