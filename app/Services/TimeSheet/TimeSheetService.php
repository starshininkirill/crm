<?php

namespace App\Services\TimeSheet;

use App\Helpers\DateHelper;
use App\Models\Department;
use App\Models\User;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

final class TimeSheetService
{
    public string $status = 'active';

    public function __construct(
        private UserService $userService,
        private SalaryCalculatorService $salaryCalculatorService,
        private TimeTrackingService $timeTrackingService,
    ) {
    }

    public function generateUsersReport(Collection $departments, Carbon $date): Collection
    {
        $result = $departments->mapWithKeys(function ($department) use ($date) {
            $users = $this->processDepartmentUsers($department, $date);
            return [$department->name => $users];
        });

        $usersWithoutDepartment = $this->getUsersWithoutDepartment($date);
        $result['Без отдела'] = $usersWithoutDepartment;

        return $result->filter(fn (Collection $department) => !$department->isEmpty());
    }

    private function processDepartmentUsers(Department $department, Carbon $date): Collection
    {
        $users = $this->salaryCalculatorService->calculateDepartmentSalary($department, $date->copy()->startOfMonth()->subMonth(), $this->status);
        $this->loadRelationsForUsers($users, $date);
        return $this->calculateGeneralData($users, $date);
    }

    private function getUsersWithoutDepartment(Carbon $date): Collection
    {
        if (!$date || DateHelper::isCurrentMonth($date)) {
            $users = User::whereNull('department_id')->with('position')->get();
        } else {
            $query = User::getLatestHistoricalRecordsQuery($date)
                ->whereNull('new_values->department_id');
            $users = User::recreateFromQuery($query, ['position']);
        }

        $users = $this->userService->filterUsersByStatus($users, $this->status, $date);

        $this->loadRelationsForUsers($users, $date);
        return $this->calculateGeneralData($users, $date);
    }

    private function calculateGeneralData(Collection|EloquentCollection $users, Carbon $date): Collection
    {
        return $users->map(function ($user) use ($date) {
            return $this->generateUserReport($user, $date);
        });
    }

    public function generateUserReport(User $user, Carbon $date): array
    {
        
        if (!isset($user->bonuses)) {
            $user->bonuses = $this->salaryCalculatorService->calculateUserBonus($user, $date);
        }

        $daysReport = $this->timeTrackingService->userMonthReport($user, $date);
        $workingDays = DateHelper::getWorkingDaysInMonth($date);
        $totalWorkingHoursInMonth = count($workingDays) * 9;

        $salary = (float) $user->getSalary();
        $hourSalary = $totalWorkingHoursInMonth > 0 ? $salary / $totalWorkingHoursInMonth : 0;

        $timeData = $this->timeTrackingService->calculateTimeData($daysReport, $workingDays);
        $salaryData = $this->salaryCalculatorService->calculateSalaryData($user, $timeData, $salary, $hourSalary);
        $adjustmentData = $this->salaryCalculatorService->calculateAdjustmentData($user);

        
        $salaryData['amount_first_half_salary'] += $adjustmentData['first_half_adjustments'];
        $salaryData['amount_second_half_salary'] += $adjustmentData['second_half_adjustments'];

        $compensationPercent = $user->employmentDetail->employmentType->compensation ?? 0;
        $salaryData['amount_first_half_salary_with_compensation'] = $salaryData['amount_first_half_salary'] + $salaryData['amount_first_half_salary'] * $compensationPercent / 100;
        $salaryData['amount_second_half_salary_with_compensation'] = $salaryData['amount_second_half_salary'] + $salaryData['amount_second_half_salary'] * $compensationPercent / 100;

        return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'surname' => $user->surname,
                'payment_account' => $user->employmentDetail?->payment_account,
                'fired_at' => $user->fired_at,
                'days' => $daysReport,
                'salary' => $salary,
                'part_salary' => $salary / 2,
                'hour_salary' => $hourSalary,
                'bonuses' => $user->bonuses,
                'position' => $user->position,
                'employment_detail' => $user->employmentDetail,
                'employment_type_id' => $user->employmentDetail?->employment_type_id,
            ] + $timeData + $salaryData + $adjustmentData;
    }

    public function loadRelationsForUsers(EloquentCollection $users, Carbon $date): void
    {
        if ($users->isEmpty()) {
            return;
        }

        $dateStart = $date->copy()->startOfMonth();
        $dateEnd = $date->copy()->endOfMonth();

        $users->loadMissing(
            [
                'department',
                'employmentDetail.employmentType',
                'position',
                'departmentHead',
                'dailyWorkStatuses' => function ($query) use ($dateStart, $dateEnd) {
                    $query->whereBetween('date', [$dateStart, $dateEnd])
                        ->with(['workStatus']);
                },
                'timeChecks' => function ($query) use ($dateStart, $dateEnd) {
                    $query->whereBetween('date', [$dateStart, $dateEnd]);
                },
                'adjustments' => function ($query) use ($dateStart, $dateEnd) {
                    $query->whereBetween('date', [$dateStart, $dateEnd]);
                },
            ]
        );
    }
}
