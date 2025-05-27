<?php

namespace App\Services\SaleReports\Generators;

use App\Models\Department;
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
        $report['usersCount'] = $users->count();
        $report['newMoney'] = $mainReportData->newMoney;

        $users->each(function ($user) use ($mainReportData, &$report) {
            $reportData = $this->reportDTOBuilder->buildHeadSubReport($mainReportData, $user);

            $report['generalPlan'] = $report['generalPlan'] + $reportData->monthWorkPlanGoal;

            $monthResult = $this->departmentPlanCalculator->monthPlan($reportData);

            if ($monthResult['completed']) {
                $report['completed'] = $report['completed'] + 1;
            }
        });

        $report['completed_percent'] = round($report['completed'] * $userPercentageWeight);

        //TODO
        // Поменять на план
        $headFullBonus = ($report['newMoney'] / 100) * 3.5;
        $report['headBonus'] = ($headFullBonus / 100) * (100 - $report['completed_percent']);

        
    }
}
