<?php

declare(strict_types=1);

namespace App\Services\ProjectReports\Generators;

use App\Models\Department;
use App\Models\WorkPlan;
use App\Services\ProjectReports\Generators\DepartmentReportGenerator;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HeadReportGenerator
{

    public function __construct(
        private DepartmentReportGenerator $departmentReportGenerator,
        private WorkPlanService $workPlanService,
        private Collection $workPlans,
    ) {}

    public function generateHeadReport(Carbon $date, Collection $departments = null): Collection
    {
        if ($departments === null) {
            $departments = Department::where('type', Department::DEPARTMENT_PROJECT_MANAGERS)
                ->whereNotNull('head_id')
                ->get();
        }

        if ($departments->isEmpty()) {
            return collect([]);
        }

        $headDepartments = $departments->whereNull('parent_id')->first();

        $this->workPlans = $this->workPlanService->actualPlans($date, $headDepartments);

        $report = $departments->map(function ($department) use ($date) {
            return $this->processUser($department, $date);
        });

        return $report;
    }

    protected function processUser(Department $department, $date): array
    {
        $departmentReport = $this->departmentReportGenerator->generateFullReport($department, $date, false);

        $upsells = $departmentReport->sum('upsells_money');
        $accountsReceivable = $departmentReport->sum('accounts_receivable_sum');

        $b1PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b1', WorkPlan::HEAD_B1_PLAN);
        $b2PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b2', WorkPlan::HEAD_B2_PLAN);
        $b4PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b4', WorkPlan::HEAD_B4_PLAN);

        $totalBonus = $b1PlanResult['bonus']
            + $b2PlanResult['bonus']
            + $b4PlanResult['bonus'];

        return [
            'user' => $department->head->only('id', 'full_name'),
            'upsells' => $upsells,
            'accounts_receivable' => $accountsReceivable,
            'b1' => $b1PlanResult,
            'b2' => $b2PlanResult,
            'b4' => $b4PlanResult,
            'bonuses' => $totalBonus,
        ];
    }

    protected function calculatePlanByCompletedCount(Collection $departmentReport, string $personalPlanKey, string $headPlanType): array
    {
        $reportWithoutProbation = $departmentReport->filter(function ($pmReport) {
            return !$pmReport['user']['is_probation'];
        });

        $defaultResult = [
            'bonus' => 0,
            'completed' => false,
            'completed_count' => 0,
            'employees_count' => $reportWithoutProbation->count(),
        ];

        $plan = $this->workPlans->where('type', $headPlanType)->first();

        if (!$plan) {
            return $defaultResult;
        }

        $minCompleted = $plan->data['goal'] ?? 0;
        $bonusPerPm = $plan->data['bonus'] ?? 0;

        if ($minCompleted <= 0 || $bonusPerPm <= 0) {
            return $defaultResult;
        }

        $completedPmsCount = $reportWithoutProbation->filter(
            function ($pmReport) use ($personalPlanKey) {
                return $pmReport[$personalPlanKey]['completed'] ?? false;
            }
        )->count();


        if ($completedPmsCount >= $minCompleted) {
            return [
                'bonus' => $completedPmsCount * $bonusPerPm,
                'completed' => true,
                'completed_count' => $completedPmsCount,
                'employees_count' => $reportWithoutProbation->count(),
            ];
        }

        return array_merge($defaultResult, ['completed_count' => $completedPmsCount]);
    }
}
