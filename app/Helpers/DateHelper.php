<?php
namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DateHelper
{
    public static function getWorkingDaysInMonth(Carbon $date): array
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $days = [];

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $day) {
            if ($day->isWeekday()) {
                $days[] = $day->format('Y-m-d');
            }
        }

        return $days;
    }
}