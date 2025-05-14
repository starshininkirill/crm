<?php

namespace App\Services\SaleDepartmentServices;

use App\DTO\SaleDepartment\ReportDTO;
use App\Factories\SaleDepartment\ReportFactory;
use App\Models\User;
use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\Payment;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\SaleDepartmentServices\PlansService;

class ReportService
{
    protected PlansService $planService;
    protected ReportDTO $fullData;
    protected ReportFactory $reportFactory;

    public function __construct(ReportDTO $reportInfo)
    {
        $this->planService = new PlansService;
        $this->reportFactory = new ReportFactory;
        $this->fullData = $reportInfo;
    }

    public function generalPlan($pivotUsers): Collection
    {
        $res = collect([
            'monthPlan' => 0,
            'needOnDay' => 0,
            'faktOnDay' => 0,
            'difference' => 0,
            'prognosis' => 0
        ]);

        $today = date("Y-m-d");
        $pastDates = $this->fullData->workingDays->filter(function ($date) use ($today) {
            return $date <= $today;
        });
        $countPastDates = count($pastDates);
        $countWorkingDays = count($this->fullData->workingDays);

        foreach ($pivotUsers as $user) {
            $res['monthPlan'] += $user['monthPlan']['goal'];
        }

        if ($countPastDates > 0) {
            $res['faktOnDay'] = $this->fullData->newMoney / $countPastDates;
        } else {
            $res['faktOnDay'] = $this->fullData->newMoney;
        }

        $res['needOnDay'] = $res['monthPlan'] / $countWorkingDays;

        $res['difference'] = ($res['faktOnDay'] * $countPastDates) - ($res['needOnDay'] * $countPastDates);

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
        $report['weeksPlan'] = $this->planService->weeksReport();
        $report['totalValues'] = $this->planService->totalValues();

        return $report;
    }

    public function motivationReport(User $user): Collection
    {
        $report = collect();

        $reportInfo = $this->reportFactory->createUserSubReport($this->fullData, $user);

        $this->planService->prepareData($reportInfo);
        $report['monthPlan'] = $this->planService->monthPlan();
        $report['doublePlan'] = $this->planService->doublePlan();
        $report['bonusPlan'] = $this->planService->bonusPlan();
        $report['weeksPlan'] = $this->planService->weeksPlan();
        $report['superPlan'] = $this->planService->superPlan($report['weeksPlan']);
        $report['totalValues'] = $this->planService->totalValues();
        $report['b1'] = $this->planService->b1Plan();
        $report['b2'] = $this->planService->b2Plan();
        $report['b3'] = $this->planService->b3Plan();
        $report['b4'] = $this->planService->b4Plan();
        $report['salary'] = $this->planService->calculateSalary($report);

        return $report;
    }

    public function unusedPayments(ReportDTO $fullData)
    {
        $unusedPayments = $fullData->payments->diff($fullData->usedPayments);

        $newMoney = $unusedPayments->where('type', Payment::TYPE_NEW)->sum('value');
        $oldMoney = $unusedPayments->where('type', Payment::TYPE_OLD)->sum('value');
        $allMoney = $newMoney + $oldMoney;

        return collect([
            'newMoney' => $newMoney,
            'oldMoney' => $oldMoney,
            'allMoney' => $allMoney,
        ]);
    }


    public function monthByDayReport(User|null $user = null): Collection
    {
        if ($this->fullData && $user != null) {
            $reportInfo = $this->reportFactory->createUserSubReport($this->fullData, $user);;
        } else {
            $reportInfo = $this->fullData;
        }

        $report = collect();
        $newPaymentsGroupedByDate = $this->groupPaymentsByDate(
            optional($reportInfo->newPayments)->isNotEmpty() ? $reportInfo->newPayments : collect(),
            $reportInfo->workingDays
        );
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
            'date' => Carbon::parse($day)->format('d.m.y'),
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
        return $payments->groupBy(function ($payment) use ($workingDays) {
            $paymentDate = Carbon::parse($payment->created_at);

            return $workingDays->contains($paymentDate)
                ? $paymentDate
                : DateHelper::getNearestPreviousWorkingDay($paymentDate, $this->fullData->workingDays);
        });
    }
}
