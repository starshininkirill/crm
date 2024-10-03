<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class SaleDepartmentService
{

    private $newMoney = 0;
    private $oldMoney = 0;
    private $mounthWorkPlan;
    private $mounthWorkPlanGoal;
    private $workingDays;
    private $payments;
    private $newPayments;
    private $oldPayments;

    public function generateUserMotivationReportData(Carbon $date, User $user): array
    {

        $report = [];

        $department = $user->department;

        $this->workingDays = DateHelper::getWorkingDaysInMonth($date);
        $nowDate = Carbon::now();

        // Получаем сумму для выполнения месячного плана
        $this->mounthWorkPlan = $this->getMounthPlan($user);
        $this->mounthWorkPlanGoal = $this->mounthWorkPlan->goal;

        // Получаем все платежи за месяц
        $this->payments = $this->getPaymentsForUserGroupByType($date, $user);

        // Группируем платежи
        $this->newPayments = $this->payments->has(Payment::TYPE_NEW) ? $this->payments->get(Payment::TYPE_NEW) : collect();
        $uniqueNewPayments = $this->newPayments->unique('contract_id');
        $this->oldPayments = $this->payments->has(Payment::TYPE_OLD) ? $this->payments->get(Payment::TYPE_OLD) : collect();

        // Получаем сумму новых и старых денег
        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');
        $allMoney = $this->newMoney + $this->oldMoney;

        // Наполняем отчёт
        $report['mounth_plan'] = $this->generateMounthPlanReport();
        $report['double_plan'] = $this->generateDoubleMounthPlanReport();
        $report['bonus_plan'] = $this->generateBonusMounthPlanReport();
        $report['weeks_plan'] = $this->generateWeeksMounthPlanReport($date);

        return $report;
    }

    private function generateWeeksMounthPlanReport(Carbon $date): array
    {
        $res = [];

        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $weekPlan = $this->mounthWorkPlanGoal / 4;

        // Начинаем с первого дня месяца
        $current = $startOfMonth->copy();
        
        // Цикл до конца месяца
        while ($current->lte($endOfMonth)) {
            $startOfWeek = $current->copy();

            if($startOfWeek->gt($endOfMonth)){
                break;
            }
            
            // Устанавливаем конец недели, но не позже конца месяца
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            
            // Если конец недели за пределами месяца, ограничиваем его концом месяца
            if ($endOfWeek->gt($endOfMonth)) {
                $endOfWeek = $endOfMonth;
            }
        
            // Добавляем неделю в массив
            $weeks[] = [
                'start' => $startOfWeek->format('Y-m-d'),
                'end' => $endOfWeek->format('Y-m-d'),
            ];
        
            // Переходим к следующему дню после конца текущей недели
            $current = $endOfWeek->addDay();
        
            // Если текущая дата больше конца месяца, выходим из цикла
            if ($current->gt($endOfMonth)) {
                break;
            }
        }

        dd($weeks);

        foreach ($weeks as $week) {

            $filteredPayments = $this->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['start'], $week['end']);
            });
            $res[] = $filteredPayments;
        };
        dd($res);

        return $res;
    }

    private function generateMounthPlanReport(): array
    {

        return  [
            'plan' => $this->mounthWorkPlanGoal,
            'value' => $this->newMoney,
            'completed' => $this->newMoney >= $this->mounthWorkPlanGoal ? true : false,
        ];
    }

    private function generateBonusMounthPlanReport(): array
    {
        $res = [
            'value' => $this->newMoney,
            'completed' => false,
            'bonus' => 0
        ];

        $planInstance = WorkPlan::where('type', WorkPlan::BONUS_PLAN)
            ->where('department_id', Department::getMainSaleDepartment()->id)
            ->first();

        if ($planInstance) {
            $plan = $planInstance->goal;

            $res['plan'] = $plan;

            if ($this->newMoney >= $plan && $plan != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->bonus;
            }
        }


        return $res;
    }
    private function generateDoubleMounthPlanReport(): array
    {

        $res =  [
            'plan' => $this->mounthWorkPlanGoal * 2,
            'value' => $this->newMoney,
            'completed' => false,
            'bonus' => 0
        ];

        $completed = $this->newMoney >= $this->mounthWorkPlanGoal * 2 ? true : false;

        if ($completed) {
            $res['completed'] = true;

            $planInstance = WorkPlan::where('type', WorkPlan::DOUBLE_PLAN)
                ->where('department_id', Department::getMainSaleDepartment()->id)
                ->first();

            if ($planInstance) {
                $res['bonus'] = $planInstance->bonus;
            }
        }

        return $res;
    }

    private function getMounthPlan(User $user)
    {
        $employmentDate = $user->getFirstWorkingDay();
        $nowDate = Carbon::now();

        $startWorkingDay = $employmentDate->format('d');

        $monthsWorked = $employmentDate->floorMonth()->diffInMonths($nowDate->floorMonth()) + 1;
        if ($startWorkingDay > 7) {
            $monthsWorked--;
        }

        $departmentId = Department::getMainSaleDepartment()->id;
        $userPositionId = $user->position->id;

        $mounthPlan = WorkPlan::where('department_id', $departmentId)
            ->where('position_id', $userPositionId)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->first();

        if ($mounthPlan) {
            return $mounthPlan;
        }

        $mounthPlan = WorkPlan::where('department_id', $departmentId)
            ->where('mounth', $monthsWorked)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->first();

        if ($mounthPlan) {
            return $mounthPlan;
        }

        return WorkPlan::where('department_id', $departmentId)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->orderBy('mounth', 'desc')
            ->first();
    }

    public function generateUserReportData(Carbon $date, User $user): Collection
    {
        $report = collect();
        $workingDays = DateHelper::getWorkingDaysInMonth($date);
        $payments = $this->getPaymentsForUserGroupByType($date, $user);

        if ($payments->has(Payment::TYPE_NEW)) {
            $newPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW] ?? collect(), $workingDays);
            $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW]->unique('contract_id') ?? collect(), $workingDays);
        } else {
            $newPaymentsGroupedByDate = collect();
            $uniqueNewPaymentsGroupedByDate = collect();
        }

        if ($payments->has(Payment::TYPE_OLD)) {
            $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_OLD] ?? collect(), $workingDays);
        } else {
            $oldPaymentsGroupedByDate = collect();
        }

        foreach ($workingDays as $day) {
            $dayFormatted = Carbon::parse($day)->format('Y-m-d');
            $report[] = $this->generateDailyReport($dayFormatted, $newPaymentsGroupedByDate, $oldPaymentsGroupedByDate, $uniqueNewPaymentsGroupedByDate);
        }

        return $report;
    }

    private function getPaymentsForUserGroupByType(Carbon $date, User $user): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $contractIds = $user->contracts->pluck('id')->unique();


        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('contract_id', $contractIds)
            ->where('status', Payment::STATUS_CLOSE)
            ->get()
            ->groupBy('type');
    }

    private function groupPaymentsByDate(Collection $payments, array $workingDays): Collection
    {
        return $payments->groupBy(function ($payment) use ($workingDays) {
            $paymentDate = Carbon::parse($payment->created_at)->format('Y-m-d');

            return in_array($paymentDate, $workingDays)
                ? $paymentDate
                : $this->getNearestPreviousWorkingDay($paymentDate, $workingDays);
        });
    }

    private function getNearestPreviousWorkingDay(string $date, array $workingDays): string
    {
        $date = Carbon::parse($date);

        while (!in_array($date->format('Y-m-d'), $workingDays)) {
            $date->subDay();
        }

        return $date->format('Y-m-d');
    }

    private function generateDailyReport(string $day, Collection $newPaymentsGroupedByDate, Collection $oldPaymentsGroupedByDate, Collection $uniqueNewPaymentsGroupedByDate): array
    {
        $dayNewPayments = $newPaymentsGroupedByDate->get($day, collect());
        $dayOldPayments = $oldPaymentsGroupedByDate->get($day, collect());
        $uniqueDayNewPayments = $uniqueNewPaymentsGroupedByDate->get($day, collect());

        $newPaymentsSum = $dayNewPayments->sum('value');
        $oldPaymentsSum = $dayOldPayments->sum('value');
        $serviceCounts = $this->calculateServiceCounts($uniqueDayNewPayments);

        return [
            'date' => $day,
            'newMoney' => $newPaymentsSum,
            'oldMoney' => $oldPaymentsSum,
            'individualSites' => $serviceCounts[ServiceCategory::INDIVIDUAL_SITE],
            'readiesSites' => $serviceCounts[ServiceCategory::READY_SITE],
            'rk' => $serviceCounts[ServiceCategory::RK],
            'seo' => $serviceCounts[ServiceCategory::SEO],
            'other' => $serviceCounts[ServiceCategory::OTHER],
        ];
    }

    private function calculateServiceCounts(Collection $dayPayments): array
    {
        $counts = [
            ServiceCategory::INDIVIDUAL_SITE => 0,
            ServiceCategory::READY_SITE => 0,
            ServiceCategory::RK => 0,
            ServiceCategory::SEO => 0,
            ServiceCategory::OTHER => 0,
        ];

        foreach ($dayPayments->unique('contract_id') as $payment) {
            foreach ($payment->contract->services as $service) {
                $this->incrementServiceCount($counts, $service->category->type);
            }
        }

        return $counts;
    }

    private function incrementServiceCount(array &$counts, string $serviceType): void
    {
        switch ($serviceType) {
            case ServiceCategory::INDIVIDUAL_SITE:
                $counts[ServiceCategory::INDIVIDUAL_SITE]++;
                break;
            case ServiceCategory::READY_SITE:
                $counts[ServiceCategory::READY_SITE]++;
                break;
            case ServiceCategory::RK:
                $counts[ServiceCategory::RK]++;
                break;
            case ServiceCategory::SEO:
                $counts[ServiceCategory::SEO]++;
                break;
            case ServiceCategory::OTHER:
                $counts[ServiceCategory::OTHER]++;
                break;
        }
    }
}
