<?php

namespace App\Services\TimeSheet;

use App\Helpers\DateHelper;
use App\Models\TimeTracking\DailyWorkStatus;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\User;
use App\Models\Finance\UserAdjustment;
use App\Models\TimeTracking\WorkStatus;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use App\Services\SaleReports\Generators\DepartmentReportGenerator;
use App\Services\SaleReports\Generators\HeadsReportGenerator;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use App\Services\ProjectReports\Generators\DepartmentReportGenerator as ProjectReportGenerator;
use App\Services\ProjectReports\Generators\HeadReportGenerator as ProjectHeadReportGenerator;

final class SalaryCalculatorService
{
    public function __construct(
        private ReportDTOBuilder $reportDTOBuilder,
        private DepartmentReportGenerator $serviceReportGenerator,
        private HeadsReportGenerator $saleHeadsReportGenerator,
        private UserService $userService,
        private ProjectReportGenerator $projectReportGenerator,
        private ProjectHeadReportGenerator $projectHeadReportGenerator,
    ) {}

    public function calculateDepartmentSalary(Department $department, Carbon $date, string $status): EloquentCollection
    {
        if ($department->type === Department::DEPARTMENT_SALE) {
            return $this->calculateSaleDepartmentSalary($department, $date, $status);
        } else if ($department->type === Department::DEPARTMENT_PROJECT_MANAGERS) {
            return $this->calculateProjectManagersDepartmentSalary($department, $date, $status);
        }

        return $department->users;
    }

    private function calculateProjectManagersDepartmentSalary(Department $department, Carbon $date, string $status): EloquentCollection
    {
        $departmentUsers = $this->userService->filterUsersByStatus(
            $department->allUsers($date, ['departmentHead', 'position']),
            $status,
            $date
        );

        if ($departmentUsers->isEmpty()) {
            return new EloquentCollection();
        }

        $reportDate = $date->copy()->startOfMonth()->subMonth();
        $report = $this->projectReportGenerator->generateFullReport($department, $reportDate, false);
        $headReport = $this->projectHeadReportGenerator->generateHeadReport($reportDate);

        if ($headReport->isNotEmpty()) {
            $report->push($headReport->first());
        }

        $reportUsersData = $report->keyBy(fn($reportItem) => $reportItem['user']->id);

        $departmentUsers->each(function (User $user) use ($reportUsersData) {
            $reportData = $reportUsersData->get($user->id);
            if ($reportData) {
                $user->bonuses = $reportData['bonuses'] ?? 0;
                $user->total_services = $reportData['total_services'] ?? 0;
            }
        });

        $finalUsers = $departmentUsers->filter(fn(User $user) => $reportUsersData->has($user->id));

        return new EloquentCollection($finalUsers->values());
    }

    private function calculateSaleDepartmentSalary(Department $department, Carbon $date, string $status): EloquentCollection
    {
        $findUsersDate = DateHelper::isCurrentMonth($date) ? null : $date;

        $departmentUsers = $this->userService->filterUsersByStatus(
            $department->allUsers($findUsersDate, ['departmentHead', 'position']),
            $status,
            $date
        );

        $reportDate = $date->copy()->startOfMonth()->subMonth();
        
        $head = $departmentUsers->firstWhere('id', $department->head_id);

        if ($head) {
            $headReport = $this->saleHeadsReportGenerator->generateHeadReport($department, $reportDate);
            $head->bonuses = $headReport['report']['headBonus'] + $headReport['report']['bonus'];
        }

        $users = $departmentUsers->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        });

        if ($users->isNotEmpty()) {
            $reportData = $this->reportDTOBuilder->buildFullReport($reportDate, $department);
            $pivotUsers = $this->serviceReportGenerator->pivotUsers($reportData, $users)->keyBy('user.id');

            $users->each(function ($user) use ($pivotUsers) {
                $userReport = $pivotUsers->get($user->id);
                $user->bonuses = $userReport['salary']['amount'] ?? 0;
            });
        }

        $otherUsers = $departmentUsers->where('id', '!=', $department->head_id);
        $sortedUsers = $otherUsers->sortBy('full_name');

        if ($head) {
            $sortedUsers->prepend($head);
        }

        return new EloquentCollection($sortedUsers->values());
    }

    public function calculateUserBonus(User $user, Carbon $date): float
    {
        if (!$user->department) {
            return 0;
        }

        $bonus = 0;

        switch ($user->department->type) {
            case Department::DEPARTMENT_SALE:
                $user->loadMissing('departmentHead');

                if ($user->id === $user->department->head_id) {
                    $headReport = $this->saleHeadsReportGenerator->generateHeadReport($user->department, $date);
                    $bonus = $headReport['report']['headBonus'] + $headReport['report']['bonus'];
                } elseif ($user->departmentHead->isEmpty()) {
                    $reportData = $this->reportDTOBuilder->buildSingleUserReport($date, $user);
                    $pivotUsers = $this->serviceReportGenerator->pivotUsers($reportData, new EloquentCollection([$user]))->keyBy('user.id');
                    $userReport = $pivotUsers->get($user->id);
                    $bonus = $userReport['salary']['amount'] ?? 0;
                }
                break;
            default:
                // В будущем здесь будет логика для других отделов
                break;
        }

        return $bonus;
    }

    public function calculateSalaryData(User $user, array $timeData, float $salary, float $hourSalary): array
    {
        $firstHalfHoursMoney = 0;
        $amountFirstHalfSalary = 0;
        if ($timeData['first_half_user_working_hours'] > 0) {
            $firstHalfHoursMoney = $timeData['first_half_hours'] * $hourSalary;
            $amountFirstHalfSalary = $user->bonuses + ($salary / 2) + $firstHalfHoursMoney;
        } else {
            $amountFirstHalfSalary = $user->bonuses;
        }

        $secondHalfHoursMoney = 0;
        $amountSecondHalfSalary = 0;
        if ($timeData['second_half_user_working_hours'] > 0) {
            $secondHalfHoursMoney = $timeData['second_half_hours'] * $hourSalary;
            $amountSecondHalfSalary = ($salary / 2) + $secondHalfHoursMoney;
        }

        return [
            'first_half_hours_money' => $firstHalfHoursMoney,
            'second_half_hours_money' => $secondHalfHoursMoney,
            'amount_first_half_salary' => $amountFirstHalfSalary,
            'amount_second_half_salary' => $amountSecondHalfSalary,
        ];
    }

    public function calculateAdjustmentData(User $user): array
    {
        $firstHalf = $user->adjustments->where('period', UserAdjustment::PERIOD_FIRST_HALF);
        $secondHalf = $user->adjustments->where('period', UserAdjustment::PERIOD_SECOND_HALF);

        $latesPenalty = $this->calculateLatesPenalty($user);

        $firstHalfAdjustments = $this->calculateAdjustments($firstHalf) - $latesPenalty;
        $secondHalfAdjustments = $this->calculateAdjustments($secondHalf);

        return [
            'first_half' => $firstHalf,
            'second_half' => $secondHalf,
            'first_half_adjustments' => $firstHalfAdjustments,
            'second_half_adjustments' => $secondHalfAdjustments,
            'lates_penalty' => $latesPenalty,
        ];
    }

    private function calculateLatesPenalty(User $user): int
    {
        $lates = $user->lateWorkStatuses
            ->where('status', DailyWorkStatus::STATUS_APPROVED);

        if ($lates->isEmpty()) {
            return 0;
        }

        return $lates->sum('money');
    }

    private function calculateAdjustments($adjustments): int
    {
        $bonuses = $adjustments
            ->where('type', UserAdjustment::TYPE_BONUS)
            ->sum('value');

        $penalties = $adjustments
            ->where('type', UserAdjustment::TYPE_PENALTY)
            ->sum('value');

        return (int)($bonuses - $penalties);
    }
}
