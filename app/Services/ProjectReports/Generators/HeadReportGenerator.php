<?php

declare(strict_types=1);

namespace App\Services\ProjectReports\Generators;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\Department;
use App\Models\WorkPlan;
use App\Models\WorkStatus;
use App\Services\ProjectReports\Generators\DepartmentReportGenerator;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

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
        $departmentReport = $this->departmentReportGenerator->generateFullReport($department, $date);

        $users = new EloquentCollection($departmentReport->pluck('user')->filter(fn($user) => !$user['is_probation']));

        $this->loadSkippedDays($users, $date);

        $upsells = $departmentReport->sum('upsells_money');
        $accountsReceivable = $departmentReport->sum('accounts_receivable_sum');

        $b1PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b1', WorkPlan::HEAD_B1_PLAN);
        $b2PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b2', WorkPlan::HEAD_B2_PLAN);
        $b3PlanResult = $this->calculateB3Plan($departmentReport);
        $b4PlanResult = $this->calculatePlanByCompletedCount($departmentReport, 'b4', WorkPlan::HEAD_B4_PLAN);
        $upsalesPlanResult = $this->calculateUpsalePlan($departmentReport, $date);

        $totalBonus = $b1PlanResult['bonus']
            + $b2PlanResult['bonus']
            + $b4PlanResult['bonus']
            + $b3PlanResult['bonus']
            + $upsalesPlanResult['bonus'];

        $head = $department->head;
        $head->bonuses = $totalBonus;

        return [
            'user' => $head,
            'upsells' => $upsells,
            'accounts_receivable' => $accountsReceivable,
            'b1' => $b1PlanResult,
            'b2' => $b2PlanResult,
            'b3' => $b3PlanResult,
            'b4' => $b4PlanResult,
            'upsales_bonus' => $upsalesPlanResult,
            'bonuses' => $totalBonus,
        ];
    }

    protected function calculateUpsalePlan(Collection $departmentReport, Carbon $date): array
    {
        $upsales = $departmentReport->sum('upsells_money');
        $upsalesPlan = $this->workPlans->where('type', WorkPlan::HEAD_UPSALE_PLAN)->first();

        $defaultResult = [
            'bonus' => 0,
            'goal' => 0,
            'upsales' => 0,
        ];

        if (!$upsalesPlan || $upsales <= 0 || $departmentReport->isEmpty()) {
            return $defaultResult;
        }

        $reportWithoutProbation = $departmentReport->filter(fn($pmReport) => !$pmReport['user']['is_probation']);

        $planGoal = $upsalesPlan->data['goal'] ?? 0;
        $workingDaysInMonth = DateHelper::getWorkingDaysInMonth($date)->count();

        $goal = $reportWithoutProbation->map(function($report) use ($planGoal, $workingDaysInMonth) {
            $skippedDays = $report['user']['skipped_days'] ?? 0;
            if($skippedDays == 0){
                return $planGoal;
            }

            $planGoal = $planGoal / $workingDaysInMonth * ($workingDaysInMonth - $skippedDays);

            return $planGoal;
        })->sum();

        $defaultResult['goal'] = $goal;
        $defaultResult['upsales'] = $upsales;

        if ($upsales >= $goal) {
            $bonus = $upsales / 100 * $upsalesPlan->data['bonus'];
            return [
                'bonus' => $bonus,  
                'goal' => $goal,
                'upsales' => $upsales,
            ];
        }

        return $defaultResult;
    }

    protected function calculateB3Plan(Collection $departmentReport): array
    {
        $reportWithoutProbation = $departmentReport->filter(fn($pmReport) => !$pmReport['user']['is_probation']);
        $employeesCount = $reportWithoutProbation->count();

        $completedPmsCount = $reportWithoutProbation->filter(
            fn($pmReport) => $pmReport['b3']['completed'] ?? false
        )->count();

        $defaultResult = [
            'bonus' => 0,
            'completed_count' => $completedPmsCount,
            'employees_count' => $employeesCount,
        ];

        $plan = $this->workPlans->where('type', WorkPlan::HEAD_B3_PLAN)->first();

        if (!$plan) {
            return $defaultResult;
        }

        $planPenaltyValue = $plan->data['penalty'] ?? 0;
        $planBonusPercentage = $plan->data['bonus'] ?? 0;

        if ($planPenaltyValue <= 0 || $planBonusPercentage <= 0 || $employeesCount === 0) {
            return $defaultResult;
        }

        $totalAccountsReceivable = $departmentReport->sum('accounts_receivable_sum');
        $bonusAmount = $totalAccountsReceivable / 100 * $planBonusPercentage;

        $employeeWeight = $employeesCount > 10
            ? 100 / $employeesCount
            : $planPenaltyValue;

        $nonCompletedPmsCount = $employeesCount - $completedPmsCount;
        $totalPenaltyPercent = $nonCompletedPmsCount * $employeeWeight;

        $finalBonus = $bonusAmount * (1 - $totalPenaltyPercent / 100);

        return [
            'bonus' => max(0, $finalBonus),
            'completed_count' => $completedPmsCount,
            'employees_count' => $employeesCount,
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

    protected function loadSkippedDays(Collection $users, Carbon $date)
    {
        if ($users->isNotEmpty()) {
            $startDate = $date->copy()->startOfMonth()->startOfDay();
            $endDate = $date->copy()->endOfMonth()->endOfDay();

            $users->loadCount(['dailyWorkStatuses' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->whereHas('workStatus', function ($query) {
                        $query->whereIn('type', [WorkStatus::TYPE_OWN_DAY, WorkStatus::TYPE_SICK_LEAVE, WorkStatus::TYPE_VACATION]);
                    });
            }]);

            foreach ($users as $user) {
                $user->skipped_days = $user->daily_work_statuses_count ?? 0;
                unset($user->daily_work_statuses_count);
            }
        }
    }
}
