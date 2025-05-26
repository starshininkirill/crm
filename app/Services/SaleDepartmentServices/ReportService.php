<?php

namespace App\Services\SaleDepartmentServices;

use App\DTO\SaleDepartment\ReportDTO;
use App\Exceptions\Business\BusinessException;
use App\Factories\SaleDepartment\ReportFactory;
use App\Models\User;
use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\Department;
use App\Models\Payment;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\SaleDepartmentServices\PlansService;
use App\Services\UserServices\UserService;

class ReportService
{
    protected ReportDTO $fullData;

    public function __construct(
        protected PlansService $planService,
        protected ReportFactory $reportFactory,
        protected UserService $userService,
    ) {}

    public function generateHeadsReport(Carbon|null $date)
    {
        $departments = Department::saleDepartments()
            ->historical($date, ['head'])
            ->whereNotNull('head');

        if ($departments->isEmpty()) return collect();

        $report = $departments->map(function ($department) use ($date) {
            $reportData = $this->reportFactory->createHeadReportData($date, $department);

            return [
                'department' => $department,
                'head' => $department->head,
                'report' => $this->headReport($department, $date, $reportData),
            ];
        });

        return $report;
    }

    protected function headReport(Department $department, Carbon|null $date, ReportDTO $mainReportData)
    {
        $report = collect();

        $users = $department->allUsers($date)->filter(function ($user) use ($department, $mainReportData) {
            return $user->id != $department->head->id;
        });

        $userPercentageWeight = 100 / $users->count();

        $report['generalPlan'] = 0;
        $report['completed'] = 0;
        $report['users_count'] = $users->count();
        $report['new_money'] = $mainReportData->newMoney;

        $users->each(function ($user) use ($mainReportData, &$report) {
            $reportData = $this->reportFactory->createHeadSubReportData($mainReportData, $user);

            $report['generalPlan'] = $report['generalPlan'] + $reportData->monthWorkPlanGoal;

            $monthResult = $this->planService->monthPlan($reportData);

            if ($monthResult['completed']) {
                $report['completed'] = $report['completed'] + 1;
            }
        });

        $report['completed_percent'] = round($report['completed'] * $userPercentageWeight);

        //TODO
        // Поменять на план
        $headFullBonus = ($report['new_money'] / 100) * 3.5;
        $report['headBonus'] = ($headFullBonus / 100) * (100 - $report['completed_percent']);


    }

    public function generateFullReport(
        Department $department,
        User $user,
        Carbon|null $date,
    ) {
        $this->fullData = $this->reportFactory->createFullReport($date, $department);

        $findUsersDate = DateHelper::isCurrentMonth($date) ? null : $date;

        $users = $department->allUsers($findUsersDate);

        if ($this->userService->getFirstWorkingDay($user)->format('Y-m') > $date->format('Y-m')) {
            throw new BusinessException('Сотрудник ещё не работал в этот месяц.');
        }

        $report = [
            'daylyReport' => $this->monthByDayReport($user),
            'motivationReport' => $this->motivationReport($user),
            'pivotWeeks' => $this->pivotWeek(),
            'pivotDaily' => $this->monthByDayReport(),
        ];

        $pivotUsers = $this->pivotUsers($users);

        return array_merge($report, [
            'pivotUsers' => $pivotUsers,
            'generalPlan' => $this->generalPlan($pivotUsers),
            'unusedPayments' => $this->unusedPayments($this->fullData),
        ]);

        return $report;
    }

    protected function generalPlan($pivotUsers): Collection
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

    protected function pivotUsers(Collection $users): Collection
    {
        $report = collect();

        foreach ($users as $user) {
            $res = $this->motivationReport($user);
            $res['name'] = $user->first_name . ' ' . $user->last_name;
            $report[] = $res;
        }

        return $report;
    }

    protected function pivotWeek(): Collection
    {
        $report = collect();

        $report['weeksPlan'] = $this->planService->weeksReport($this->fullData);
        $report['totalValues'] = $this->planService->totalValues($this->fullData);

        return $report;
    }

    protected function motivationReport(User $user): Collection
    {
        $report = collect();

        $reportInfo = $this->reportFactory->createUserSubReport($this->fullData, $user);

        $report['monthPlan'] = $this->planService->monthPlan($reportInfo);
        $report['doublePlan'] = $this->planService->doublePlan($reportInfo);
        $report['bonusPlan'] = $this->planService->bonusPlan($reportInfo);
        $report['weeksPlan'] = $this->planService->weeksPlan($reportInfo);
        $report['superPlan'] = $this->planService->superPlan($report['weeksPlan'], $reportInfo);
        $report['totalValues'] = $this->planService->totalValues($reportInfo);
        $report['b1'] = $this->planService->b1Plan($reportInfo);
        $report['b2'] = $this->planService->b2Plan($reportInfo);
        $report['b3'] = $this->planService->b3Plan($reportInfo);
        $report['b4'] = $this->planService->b4Plan($reportInfo);

        $bonuses = $this->planService->getBonuses();
        $report['salary'] = $this->planService->calculateSalary($report, $reportInfo, $bonuses);


        return $report;
    }

    protected function unusedPayments(ReportDTO $fullData)
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


    protected function monthByDayReport(User|null $user = null): Collection
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
