<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\Departments\SaleDepartment;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class SaleDepartmentService
{

    public function generateUserMotivationReportData(Carbon $date, User $user)
    {

        $report = [
            'mounth_plan' => [
                'plan' => '',
                'value' => '',
                'completed' => false,
            ],
            'double_plan' => [
                'plan' => '',
                'value' => '',
                'completed' => false,
                'bonus' => 0,
            ],
            'bonus_plan' => [
                'plan' => '',
                'value' => '',
                'completed' => false,
                'bonus' => 0,
            ],
            'super_plan' => [
                'plan' => '',
                'value' => '',
                'completed' => false,
                'bonus' => 0,
            ],
        ];

        $department = $user->department->departmentable;
        dd($department);

        $workingDays = DateHelper::getWorkingDaysInMonth($date);
        $nowDate = Carbon::now();

        $mounthWorkPlan = $this->getMounthPlan($user);

        // Получаем все платежи за месяц
        $payments = $this->getPaymentsForUserGroupByType($date, $user);

        // Группируем платежи
        $newPayments = $payments->has(Payment::TYPE_NEW) ? $payments->get(Payment::TYPE_NEW) : collect();
        $uniqueNewPayments = $newPayments->unique('contract_id');
        $oldPayments = $payments->has(Payment::TYPE_OLD) ? $payments->get(Payment::TYPE_OLD) : collect();

        // Получаем сумму новых и старых денег
        $newMoney = $newPayments->sum('value');
        $oldMoney = $oldPayments->sum('value');
        $allMoney = $newMoney + $oldMoney;

        // Получаем сумму для выполнения месячного плана
        $mounthWorkPlanGoal = $mounthWorkPlan->goal;

        // Наполняем отчёт
        $report['mounth_plan'] = $this->generateMounthPlanReport($newMoney, $mounthWorkPlanGoal);
        $report['double_plan'] = $this->generateDoubleMounthPlanReport($newMoney, $mounthWorkPlanGoal);

        // dd($report);
    }

    private function generateMounthPlanReport($money, $plan): array
    {

        return  [
            'plan' => $plan,
            'value' => $money,
            'completed' => $money >= $plan ? true : false
        ];
    }
    private function generateDoubleMounthPlanReport($money, $plan): array
    {

        return  [
            'plan' => $plan * 2,
            'value' => $money,
            'completed' => $money >= $plan * 2 ? true : false
        ];
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

        $departmentId = SaleDepartment::getMainDepartment()->id;
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

        if($payments->has(Payment::TYPE_NEW)){
            $newPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW] ?? collect(), $workingDays);
            $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW]->unique('contract_id') ?? collect(), $workingDays);
        }else{
            $newPaymentsGroupedByDate = collect();
            $uniqueNewPaymentsGroupedByDate = collect();
        }

        if($payments->has(Payment::TYPE_OLD)){
            $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_OLD] ?? collect(), $workingDays);
        }else{
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
