<?php

namespace App\Services\SaleReports\Generators;

use App\Exceptions\Business\BusinessException;
use App\Models\UserManagement\User;
use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\UserManagement\Department;
use App\Models\Services\ServiceCategory;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use App\Services\SaleReports\Plans\PlanCalculatorFactory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\UserServices\UserService;
use Illuminate\Support\Facades\DB;

class DepartmentReportGenerator extends BaseReportGenerator
{

    public function __construct(
        ReportDTOBuilder $reportDTOBuilder,
        UserService $userService,
        protected PlanCalculatorFactory $planCalculatorFactory
    ) {
        parent::__construct($reportDTOBuilder, $userService);
    }

    public function generateFullReport(Department $department, User $user, Carbon|null $date)
    {
        $reportData = $this->reportDTOBuilder->buildFullReport($date, $department);

        $findUsersDate = DateHelper::isCurrentMonth($date) ? null : $date;

        $users = $department->allUsers($findUsersDate, ['departmentHead', 'position'])->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        });

        if ($this->userService->getFirstWorkingDay($user)->format('Y-m') > $date->format('Y-m')) {
            throw new BusinessException('Сотрудник ещё не работал в этот месяц.');
        }

        $report = [
            'daylyReport' => $this->monthByDayReport($reportData, $user),
            'motivationReport' => $this->motivationReport($reportData, $user),
            'pivotWeeks' => $this->pivotWeek($reportData),
            'pivotDaily' => $this->monthByDayReport($reportData),
        ];

        $pivotUsers = $this->pivotUsers($reportData, $users);

        $report = array_merge($report, [
            'pivotUsers' => $pivotUsers,
            'generalPlan' => $this->generalPlan($reportData, $pivotUsers),
            'unusedPayments' => $this->unusedPayments($reportData),
        ]);

        return $report;
    }

    protected function generalPlan(ReportDTO $reportData, $pivotUsers): Collection
    {
        $res = collect([
            'monthPlan' => 0,
            'needOnDay' => 0,
            'faktOnDay' => 0,
            'difference' => 0,
            'prognosis' => 0
        ]);

        $today = date("Y-m-d");
        $pastDates = $reportData->workingDays->filter(function ($date) use ($today) {
            return $date <= $today;
        });
        $countPastDates = count($pastDates);
        $countWorkingDays = count($reportData->workingDays);

        foreach ($pivotUsers as $user) {
            $res['monthPlan'] += $user['monthPlan']['goal'];
        }

        if ($countPastDates > 0) {
            $res['faktOnDay'] = $reportData->newMoney / $countPastDates;
        } else {
            $res['faktOnDay'] = $reportData->newMoney;
        }

        $res['needOnDay'] = $res['monthPlan'] / $countWorkingDays;

        $res['difference'] = ($res['faktOnDay'] * $countPastDates) - ($res['needOnDay'] * $countPastDates);

        $res['prognosis'] = $res['faktOnDay'] * $countWorkingDays;

        return $res;
    }

    public function pivotUsers(ReportDTO $reportData, Collection $users): Collection
    {
        $report = collect();

        foreach ($users as $user) {
            $res = $this->motivationReport($reportData, $user);
            $res['name'] = $user->first_name . ' ' . $user->last_name;
            $res['user'] = $user;
            $report[] = $res;
        }

        return $report;
    }

    protected function pivotWeek(ReportDTO $reportData): Collection
    {
        $report = collect();
        $planCalculator = $this->planCalculatorFactory->defaultCalculator();

        $report['weeksPlan'] = $planCalculator->weeksReport($reportData);
        $report['totalValues'] = $planCalculator->totalValues($reportData);

        return $report;
    }

    protected function motivationReport(ReportDTO $reportInfo, User $user): Collection
    {
        $report = collect();

        $planCalculator = $this->planCalculatorFactory->makeFor($user);
        $reportInfo = $this->reportDTOBuilder->getUserSubdata($reportInfo, $user);

        $report['monthPlan'] = $planCalculator->monthPlan($reportInfo);
        $report['doublePlan'] = $planCalculator->doublePlan($reportInfo);
        $report['bonusPlan'] = $planCalculator->bonusPlan($reportInfo);
        $report['weeksPlan'] = $planCalculator->weeksPlan($reportInfo);
        $report['superPlan'] = $planCalculator->superPlan($report['weeksPlan'], $reportInfo);
        $report['totalValues'] = $planCalculator->totalValues($reportInfo);
        $report['b1'] = $planCalculator->b1Plan($reportInfo);
        $report['b2'] = $planCalculator->b2Plan($reportInfo);
        $report['b3'] = $planCalculator->b3Plan($reportInfo);
        $report['b4'] = $planCalculator->b4Plan($reportInfo);

        $bonuses = $planCalculator->getBonuses();
        $report['salary'] = $planCalculator->calculateSalary($report, $reportInfo, $bonuses);

        return $report;
    }


    protected function monthByDayReport(ReportDTO $reportData, User|null $user = null): Collection
    {

        if ($user != null) {
            $reportInfo = $this->reportDTOBuilder->getUserSubdata($reportData, $user);
        } else {
            $reportInfo = $reportData;
        }


        $report = collect();

        $newPaymentsGroupedByDate = $this->groupPaymentsByDate(
            $reportInfo,
            optional($reportInfo->newPayments)->isNotEmpty() ? $reportInfo->newPayments : collect()
        );
        $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate($reportInfo, optional($reportInfo->newPayments)->unique('contract_id') ?? collect());
        $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($reportInfo, optional($reportInfo->oldPayments)->isNotEmpty() ? $reportInfo->oldPayments : collect());
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
}
