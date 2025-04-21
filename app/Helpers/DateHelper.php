<?php

namespace App\Helpers;

use App\Models\FinanceWeek;
use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Carbon\CarbonPeriod;
use Exception;

class DateHelper
{
    protected static $cachedWorkingDays = [];

    public static function daysInMonth(Carbon $date): Collection
    {
        $daysInMonth = $date->daysInMonth;

        return Collection::range(1, $daysInMonth)
            ->mapWithKeys(function ($day) use ($date) {
                $dayDate = Carbon::create($date->year, $date->month, $day);
                return [$day => [
                    'date' => $dayDate,
                ]];
            });
    }

    public static function workingCalendar(int $year): Collection
    {
        $months = collect();
        $daysInstances = WorkingDay::whereYear('date', $year)->get();
        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::createFromDate($year, $month, 1);
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $weeks = [];
            $week = [];

            // Добавляем пустые дни, если месяц начинается не с понедельника
            for ($i = 1; $i < $startOfMonth->dayOfWeekIso; $i++) {
                $week[] = null;
            }

            // Заполняем дни месяца
            for ($day = 1; $day <= $endOfMonth->day; $day++) {
                $date = Carbon::createFromDate($year, $month, $day);
                $week[] = [
                    'date' => $date,
                    'is_workday' => DateHelper::isWorkingDay($date, $daysInstances),
                ];

                // Если неделя заполнена (7 дней), добавляем её в массив недель и начинаем новую неделю
                if (count($week) == 7) {
                    $weeks[] = $week;
                    $week = [];
                }
            }

            // Добавляем оставшиеся дни недели в конце месяца
            if (!empty($week)) {
                while (count($week) < 7) {
                    $week[] = null;
                }
                $weeks[] = $week;
            }

            $months[] = [
                'name' => $startOfMonth->locale('ru')->monthName,
                'weeks' => $weeks
            ];
        }
        return $months;
    }


    public static function getWorkingDaysInMonth(Carbon $date, Collection $workingDays = null): Collection
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();
        $days = collect();

        if ($workingDays == null) {
            $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        }

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $day) {
            if (self::isWorkingDay($day, $workingDays)) {
                $days[] = $day->format('Y-m-d');
            }
        }

        return $days;
    }

    public static function getWorkingDaysInRange(Carbon $startDate, Carbon $endDate): array
    {
        $workingDays = [];

        while ($startDate->lte($endDate)) {
            if (DateHelper::isWorkingDay($startDate)) {
                $workingDays[] = $startDate->toDateString();
            }

            $startDate->addDay();
        }

        return $workingDays;
    }

    public static function isWorkingDay(Carbon $date, Collection $workingDays = null): bool
    {
        $dateKey = $date->format('Y-m-d');

        if (isset(self::$cachedWorkingDays[$dateKey])) {
            return self::$cachedWorkingDays[$dateKey];
        }

        if ($workingDays !== null) {
            $dateInstance = $workingDays->where('date', $dateKey)->first();
        } else {
            $dateInstance = WorkingDay::whereDate('date', $date)->first();
        }

        $isWorkingDay = $dateInstance ? $dateInstance->isWorkingDay() : $date->isWeekday();

        self::$cachedWorkingDays[$dateKey] = $isWorkingDay;

        return $isWorkingDay;
    }

    public static function getValidatedDateOrNow(string|null $date, string $format = 'Y-m'): Carbon
    {

        if ($date != null && DateHelper::isValidYearMonth($date, $format)) {
            $date = Carbon::createFromDate($date);
        } else {
            $date = Carbon::now();
        }
        return $date;
    }

    public static function isValidYearMonth(string $date, $format = 'Y-m'): bool
    {
        try {
            $parsedDate = Carbon::createFromDate($date);

            return $parsedDate && $parsedDate->format($format) === $date;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function splitMonthIntoWeek(Carbon $date): Collection
    {
        $weeks = collect();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $financeWeeks = FinanceWeek::where('date_start', '>=',  $startOfMonth)
            ->where('date_end', '<=', $endOfMonth)
            ->get();
        if (!$financeWeeks->isEmpty()) {
            $financeWeeks->map(function ($week) {
                $week->date_start = Carbon::parse($week->date_start);
                $week->date_end = Carbon::parse($week->date_end);
                return $week;
            });
            return $financeWeeks;
        }

        // Начинаем с первого дня месяца
        $current = $startOfMonth->copy();

        // Цикл до конца месяца
        while ($current->lte($endOfMonth)) {
            $startOfWeek = $current->copy()->startOfDay();

            // Устанавливаем конец недели, но не позже конца месяца
            $endOfWeek = $startOfWeek->copy()->endOfWeek()->endOfDay();

            // Если конец недели за пределами месяца, ограничиваем его концом месяца
            if ($endOfWeek->gt($endOfMonth)) {
                $endOfWeek = $endOfMonth->copy()->endOfDay();
            }

            // Если текущая дата больше конца месяца, выходим из цикла
            if ($current->gt($endOfMonth)) {
                break;
            }

            // Добавляем неделю в массив
            $weeks[] = [
                'date_start' => $startOfWeek,
                'date_end' => $endOfWeek,
            ];
            // Переходим к следующему дню после конца текущей недели
            $current = $endOfWeek->copy()->addDay();
        };

        return $weeks;
    }

    public static function getNearestPreviousWorkingDay(Carbon $date, Collection|array $workingDays = null): string
    {
        if ($workingDays == null) {
            $workingDays = self::getWorkingDaysInMonth($date);
        }

        $date = Carbon::parse($date);
        $startOfMonth = $date->copy()->startOfMonth();

        while (!$workingDays->contains($date->format('Y-m-d'))) {
            $date->subDay();
            if ($date->lessThan($startOfMonth)) {
                return $workingDays->first();
            }
        }

        return $date->format('Y-m-d');
    }

    public static function isCurrentMonth(Carbon $date)
    {
        return $date->format('Y-m') == Carbon::now()->format('Y-m');
    }
}
