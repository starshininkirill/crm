<?php

namespace App\Services\SaleReports\Generators;

use App\Models\Department;
use App\Models\WorkPlan;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use Carbon\Carbon;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\Plans\DepartmentPlanCalculator;
use App\Services\SaleReports\Plans\HeadsPlanCalculator;
use App\Services\UserServices\UserService;

class HeadsReportGenerator extends BaseReportGenerator
{
    public function __construct(
        ReportDTOBuilder $reportDTOBuilder,
        UserService $userService,
        protected DepartmentPlanCalculator $departmentPlanCalculator,
        protected HeadsPlanCalculator $headsPlanCalculator,
    ) {
        parent::__construct($reportDTOBuilder, $userService);
    }

    public function generateHeadsReport(Carbon|null $date)
    {
        $departments = Department::saleDepartments()
            ->historical($date, ['head'])
            ->whereNotNull('head');

        if ($departments->isEmpty()) return collect();

        $report = $departments->map(function ($department) use ($date) {
            $reportData = $this->reportDTOBuilder->buildHeadReport($date, $department);

            return [
                'department' => $department->only('name', 'id'),
                'head' => $department->head->only('id', 'full_name', 'calculated_salary'),
                'report' => $this->headReport($department, $date, $reportData),
            ];
        });

        return $report;
    }

    protected function headReport(Department $department, Carbon|null $date, ReportDTO $reportData)
    {
        $report = collect();

        $users = $department->allUsers($date)->filter(function ($user) use ($department, $reportData) {
            return $user->id != $department->head->id;
        });

        $userPercentageWeight = 100 / $users->count();

        $report['generalPlan'] = 0;
        $report['completed'] = 0;
        $report['usersCount'] = $users->count();
        $report['newMoney'] = $reportData->newMoney;

        $users->each(function ($user) use ($reportData, &$report) {
            $reportData = $this->reportDTOBuilder->buildHeadSubReport($reportData, $user);

            $report['generalPlan'] = $report['generalPlan'] + $reportData->monthWorkPlanGoal;

            $monthResult = $this->departmentPlanCalculator->monthPlan($reportData);

            if ($monthResult['completed']) {
                $report['completed'] = $report['completed'] + 1;
            }
        });

        $report['completedPercent'] = round($report['completed'] * $userPercentageWeight);


        $headBonus = $reportData->workPlans->firstWhere('type', WorkPlan::HEAD_PERCENT_BONUS)?->data['bonus'] ?? 0;

        $headFullBonus = ($report['newMoney'] / 100) * $headBonus;
        if ($report['completedPercent'] > 10) {
            $report['headBonus'] = ($headFullBonus / 100) * $report['completedPercent'];
        } else {
            $minimalPercent = $reportData->workPlans->firstWhere('type', WorkPlan::HEAD_MINIMAL_PERCENT)?->data['bonus'] ?? 0;
            $report['headBonus'] = ($headFullBonus / 100) * $minimalPercent;
        }


        $b1 = $this->headsPlanCalculator->percentPlan($reportData, $report['generalPlan'], WorkPlan::HEAD_B1_PLAN);
        $b2 = $this->headsPlanCalculator->percentPlan($reportData, $report['generalPlan'], WorkPlan::HEAD_B2_PLAN);

        $report['b1'] = $b1;
        $report['b2'] = $b2;

        if ($b2['completed']) {
            $report['bonus'] = $b2['bonus'];
        } else {
            $report['bonus'] = $b1['completed'] ? $b1['bonus'] : 0;
        }

        $report['remainingAmount'] = $report['generalPlan'] - $report['newMoney'];

        return $report;
    }
}
