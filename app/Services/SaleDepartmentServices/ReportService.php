<?php

namespace App\Services\SaleDepartmentServices;

use App\Models\User;
use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\SaleDepartmentServices\PlansService;

class ReportService
{
    protected $planService;
    protected $fullData;

    public function __construct(PlansService $planService, Carbon $date)
    {
        $this->planService = $planService;
        $this->fullData = new ReportInfo($date);
    }

    public function generalPlan($pivotUsers): Collection
    {
        $res = collect([
            'mounthPlan' => 0,
            'needOnDay' => 0,
            'faktOnDay' => 0,
            'difference' => 0,
            'prognosis' => 0
        ]);

        $today = date("Y-m-d");
        $pastDates = array_filter($this->fullData->workingDays, function ($date) use ($today) {
            return $date <= $today;
        });
        $countPastDates = count($pastDates);
        $countWorkingDays = count($this->fullData->workingDays);

        foreach ($pivotUsers as $user) {
            $res['mounthPlan'] += $user['mounthPlan']['goal'];
        }

        $res['needOnDay'] = $res['mounthPlan'] / $countWorkingDays;

        $res['faktOnDay'] = $this->fullData->newMoney / $countPastDates;

        $res['difference'] = $res['faktOnDay'] * $countPastDates - $res['needOnDay'] * $countPastDates;

        $res['prognosis'] = $res['faktOnDay'] * $countWorkingDays;

        return $res;
    }

    public function pivotUsers(Collection $users): Collection
    {
        $report = collect();

        foreach ($users as $user) {
            $res = $this->motivationReport($user);
            $res['name'] = $user->first_name . ' ' . $user->last_name;
            $report[] = $res;
        }

        return $report;
    }

    public function pivotWeek(): Collection
    {
        $report = collect();

        $this->planService->prepareData($this->fullData);
        $report['weeks'] = $this->planService->weeksReport();
        $report['totalValues'] = $this->planService->totalValues();

        return $report;
    }

    public function motivationReport(User $user): Collection
    {
        $report = collect();

        $reportInfo = $this->fullData->getUserSubdata($user);

        $this->planService->prepareData($reportInfo);
        $report['mounthPlan'] = $this->planService->mounthPlan();
        $report['doublePlan'] = $this->planService->doublePlan();
        $report['bonusPlan'] = $this->planService->bonusPlan();
        $report['weeksPlan'] = $this->planService->weeksPlan();
        $report['superPlan'] = $this->planService->superPlan($report['weeksPlan']);
        $report['totalValues'] = $this->planService->totalValues();
        $report['b1'] = $this->planService->bServicesPlan(WorkPlan::B1_PLAN);
        $report['b2'] = $this->planService->bServicesPlan(WorkPlan::B2_PLAN);
        $report['b3'] = $this->planService->b3Plan();
        $report['b4'] = $this->planService->b4Plan();
        $report['salary'] = $this->planService->calculateSalary($report);

        return $report;
    }


    public function mounthByDayReport(User $user = null): Collection
    {
        if ($this->fullData && $user != null) {
            $reportInfo = $this->fullData->getUserSubdata($user);
        } else {
            $reportInfo = $this->fullData;
        }

        $report = collect();

        $newPaymentsGroupedByDate = $this->groupPaymentsByDate(optional($reportInfo->newPayments)->isNotEmpty() ? $reportInfo->newPayments : collect(), $reportInfo->workingDays);

        $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate(optional($reportInfo->newPayments)->unique('contract_id') ?? collect(), $reportInfo->workingDays);

        $oldPaymentsGroupedByDate = $this->groupPaymentsByDate(optional($reportInfo->oldPayments)->isNotEmpty() ? $reportInfo->oldPayments : collect(), $reportInfo->workingDays);

        foreach ($reportInfo->workingDays as $day) {
            $dayFormatted = Carbon::parse($day)->format('Y-m-d');
            $report[] = $this->generateDailyReport($dayFormatted, $newPaymentsGroupedByDate, $oldPaymentsGroupedByDate, $uniqueNewPaymentsGroupedByDate);
        }

        return $report;
    }

    private function generateDailyReport(string $day, Collection $newPaymentsGroupedByDate, Collection $oldPaymentsGroupedByDate, Collection $uniqueNewPaymentsGroupedByDate): array
    {
        $dayNewPayments = $newPaymentsGroupedByDate->get($day, collect());
        $dayOldPayments = $oldPaymentsGroupedByDate->get($day, collect());
        $uniqueDayNewPayments = $uniqueNewPaymentsGroupedByDate->get($day, collect());

        $newPaymentsSum = $dayNewPayments->sum('value');
        $oldPaymentsSum = $dayOldPayments->sum('value');

        $serviceCounts = ServiceCountHelper::calculateServiceCountsByPayments($uniqueDayNewPayments);

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

    private function groupPaymentsByDate(Collection $payments, $workingDays): Collection
    {
        $workingDays = $workingDays;
        return $payments->groupBy(function ($payment) use ($workingDays) {
            $paymentDate = Carbon::parse($payment->created_at);

            return in_array($paymentDate, $workingDays)
                ? $paymentDate
                : DateHelper::getNearestPreviousWorkingDay($paymentDate);
        });
    }
}
