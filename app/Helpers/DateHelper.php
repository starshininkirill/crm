<?php

namespace App\Helpers;

use App\Models\WorkingDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Carbon\CarbonPeriod;
use Exception;

class DateHelper
{
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

    public static function isWorkingDay(Carbon $date, Collection $workingDays = null): bool
    {
        
        if ($workingDays == null || $workingDays->isEmpty()) {
            $dateInstance = WorkingDay::whereDate('date', $date)->first();
        }else{
            $dateInstance = $workingDays->where('date', $date->format('Y-m-d'))->first();
        }

        if ($dateInstance) {
            return $dateInstance->isWorkingDay();
        }

        return $date->isWeekday();
    }

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

    public static function isValidYearMonth(string $date, $format = 'Y-m'): bool
    {
        try {
            $parsedDate = Carbon::createFromDate($date);

            return $parsedDate && $parsedDate->format($format) === $date;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function splitMounthIntoWeek(Carbon $date): Collection
    {
        $weeks = collect();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

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
                'start' => $startOfWeek,
                'end' => $endOfWeek,
            ];
            // Переходим к следующему дню после конца текущей недели
            $current = $endOfWeek->copy()->addDay();
        };

        return $weeks;
    }
    public static function getNearestPreviousWorkingDay(Carbon $date): string
    {
        $workingDays = self::getWorkingDaysInMonth($date);
        $date = Carbon::parse($date);

        while (!in_array($date->format('Y-m-d'), $workingDays)) {
            $date->subDay();
        }

        return $date->format('Y-m-d');
    }
}
