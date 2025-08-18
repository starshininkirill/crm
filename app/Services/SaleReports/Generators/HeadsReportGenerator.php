<?php

namespace App\Services\SaleReports\Generators;

use App\Models\UserManagement\Department;
use App\Models\Global\WorkPlan;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use Carbon\Carbon;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\Plans\DefaultSalePlanCalculator;
use App\Services\SaleReports\Plans\HeadsPlanCalculator;
use App\Services\UserServices\UserService;
use Illuminate\Support\Collection;

class HeadsReportGenerator extends BaseReportGenerator
{
    public function __construct(
        ReportDTOBuilder $reportDTOBuilder,
        UserService $userService,
        protected DefaultSalePlanCalculator $DefaultSalePlanCalculator,
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
            return $this->generateHeadReport($department, $date);
        });

        return $report;
    }

    public function generateHeadReport(Department $department, Carbon|null $date)
    {
        $users = $department->allUsers($date, ['position']);
        $reportData = $this->reportDTOBuilder->buildHeadReport($date, $department, $users);

        return [
            'department' => $department->only('name', 'id'),
            'head' => $department->head->only('id', 'full_name', 'calculated_salary'),
            'report' => $this->headReport($department, $date, $reportData, $users),
        ];
    }

    protected function headReport(Department $department, Carbon|null $date, ReportDTO $reportData, Collection $users)
    {
        $report = collect();

        $activeUsers = $this->userService->filterUsersByStatus($users, 'active', $date)
            ->where('id', '!=', $department->head->id);

        $activeUsers->count() != 0 
            ? $userPercentageWeight = 100 / $activeUsers->count()
            : $userPercentageWeight = 100;

        $report['generalPlan'] = 0;
        $report['completed'] = 0;
        $report['usersCount'] = $activeUsers->count();
        $report['newMoney'] = $reportData->newMoney;

        $activeUsers->each(function ($user) use ($reportData, &$report) {
            $reportData = $this->reportDTOBuilder->buildHeadSubReport($reportData, $user);

            $report['generalPlan'] = $report['generalPlan'] + $reportData->monthWorkPlanGoal;

            $monthResult = $this->DefaultSalePlanCalculator->monthPlan($reportData);

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
