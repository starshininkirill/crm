<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\Department;
use App\Models\User;
use App\Models\UserAdjustment;
use App\Models\WorkStatus;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use App\Services\SaleReports\Generators\DepartmentReportGenerator;
use App\Services\SaleReports\Generators\HeadsReportGenerator;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeSheetService
{
    public $status = 'active';

    public function __construct(
        private ReportDTOBuilder $reportDTOBuilder,
        private WorkTimeService $service,
        private DepartmentReportGenerator $serviceReportGenerator,
        private HeadsReportGenerator $saleHeadsReportGenerator,
        private UserService $userService,
    ) {}

    public function newGenerateUsersReport(Collection $departments, Carbon $date)
    {
        $result = $departments->mapWithKeys(function ($department) use ($date) {
            $users = $this->processDepartmentUsers($department, $date);
            return [$department->name => $users];
        });

        $usersWithoutDepartment = $this->getUsersWithoutDepartment($date);
        $result['Без отдела'] = $usersWithoutDepartment;

        return $result->filter(function ($department) {
            return !$department->isEmpty();
        });
    }

    private function processDepartmentUsers(Department $department, Carbon $date): Collection
    {
        $users = $this->calculateSalary($department, $date);
        $this->loadRelation($users, $date);
        return $this->calculateGeneralData($users, $date);
    }

    private function getUsersWithoutDepartment(Carbon $date): Collection
    {
        if (!$date || DateHelper::isCurrentMonth($date)) {
            $users = User::whereNull('department_id')->get();
        } else {
            $query = User::getLatestHistoricalRecordsQuery($date)
                ->whereNull('new_values->department_id');
            $users = User::recreateFromQuery($query);
        }

        $users = $this->userService->filterUsersByStatus($users, $this->status, $date);

        $this->loadRelation($users, $date);
        return $this->calculateGeneralData($users, $date);
    }

    private function calculateGeneralData(Collection|EloquentCollection $users, Carbon $date)
    {
        $users = $users->map(function ($user) use ($date) {
            $daysReport = $this->userMonthReport($user, $date);
            $workingDays = DateHelper::getWorkingDaysInMonth($date);
            $salary = $user->getSalary();
            $hourSalary = $salary / (count(DateHelper::getWorkingDaysInMonth($date)) * 9);

            $fistHalf =  $user->adjustments->where('period', UserAdjustment::PERIOD_FIRST_HALF);
            $secondHalf = $user->adjustments->where('period', UserAdjustment::PERIOD_SECOND_HALF);
            
            $latesPenalty = $this->calculateLatesPenalty($user);

            $firtHalfAdjustments = $this->calculateAdjustments($fistHalf) - $latesPenalty;
            $secondHalfAdjustments = $this->calculateAdjustments($secondHalf);

            $firstHalfAmountHours = $workingDays->filter(function ($date) {
                return Carbon::parse($date)->day <= 14;
            })->count() * 9;

            $secondHalfAmountHours = $workingDays->filter(function ($date) {
                return Carbon::parse($date)->day > 14;
            })->count() * 9;

            $firstHalfUserWorkingHours = $daysReport->filter(function ($day) {
                return $day['date']->day <= 14;
            })->sum('hours');

            $secondHalfUserWorkingHours = $daysReport->filter(function ($day) {
                return $day['date']->day > 14;
            })->sum('hours');

            $firstHalfUserDiffHours = $firstHalfUserWorkingHours - $firstHalfAmountHours;
            $secondHalfUserDiffHours = $secondHalfUserWorkingHours - $secondHalfAmountHours;

            if ($firstHalfUserWorkingHours == 0) {
                $firstHalfHoursMoney = 0;
                $amountFirstHalfSalary = $user->bonuses;
            } else {
                $firstHalfHoursMoney = $firstHalfUserDiffHours * $hourSalary;
                $amountFirstHalfSalary = $user->bonuses + ($salary / 2) + $firstHalfHoursMoney;
            }

            if ($secondHalfUserWorkingHours == 0) {
                $secondHalfHoursMoney = 0;
                $amountSecondHalfSalary = 0;
            } else {
                $secondHalfHoursMoney = $secondHalfUserDiffHours * $hourSalary;
                $amountSecondHalfSalary = ($salary / 2) + $secondHalfHoursMoney;
            }

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'fired_at' => $user->fired_at,
                'days' => $daysReport,
                'salary' => $salary,
                'part_salary' => $user->salary / 2,
                'hour_salary' => $hourSalary,
                'bonuses' => $user->bonuses,
                'first_half_hours' => $firstHalfUserDiffHours,
                'second_half_hours' => $secondHalfUserDiffHours,
                'first_half_hours_money' => $firstHalfHoursMoney,
                'second_half_hours_money' => $secondHalfHoursMoney,
                'amount_first_half_salary' => $amountFirstHalfSalary + $firtHalfAdjustments,
                'amount_second_half_salary' => $amountSecondHalfSalary + $secondHalfAdjustments,
                'first_half' => $fistHalf,
                'second_half' => $secondHalf,
                'first_half_adjustments' => $firtHalfAdjustments,
                'second_half_adjustments' => $secondHalfAdjustments,
                'position' => $user->position,
                'lates_penalty' => $latesPenalty,
            ];
        });

        return $users;
    }

    private function calculateLatesPenalty(User $user): int
    {
        $lates = $user->dailyWorkStatuses
            ->where('status', DailyWorkStatus::STATUS_APPROVED)
            ->filter(function ($dailyStatus) {
                return $dailyStatus->workStatus &&
                    $dailyStatus->workStatus->type === WorkStatus::TYPE_LATE;
            });

        if ($lates->isEmpty()) {
            return 0;
        } else {
            return ($lates->count() - 1) * 300;
        }
    }

    private function calculateAdjustments($adjustments)
    {
        $bonuses = $adjustments
            ->where('type', UserAdjustment::TYPE_BONUS)
            ->sum('value');

        $penalties = $adjustments
            ->where('type', UserAdjustment::TYPE_PENALTY)
            ->sum('value');

        return $bonuses - $penalties;
    }

    private function calculateSalary(Department $department, Carbon $date)
    {
        if ($department->type == Department::SALE_DEPARTMENT) {
            return $this->calculateSaleDepartmentSalary($department, $date);
        }

        return $department->users;
    }

    private function calculateSaleDepartmentSalary(Department $department, Carbon $date): EloquentCollection
    {
        $findUsersDate = DateHelper::isCurrentMonth($date) ? null : $date;

        $departmentUsers = $this->userService->filterUsersByStatus($department->allUsers($findUsersDate, ['departmentHead', 'position']), $this->status, $date);

        $head = $this->userService->filterUsersByStatus(collect([$department->head]), $this->status, $date)->first();

        if ($head) {
            $headReport = $this->saleHeadsReportGenerator->generateHeadReport($department, $date);

            $head->bonuses = $headReport['report']['headBonus'] + $headReport['report']['bonus'];
        }

        $users = $departmentUsers->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        });

        $reportData = $this->reportDTOBuilder->buildFullReport($date, $department);

        $pivotUsers = $this->serviceReportGenerator->pivotUsers($reportData, $users);

        $users = $pivotUsers->map(function ($userReport) use ($date) {
            $user = $userReport['user'];
            $user->bonuses = $userReport['salary']['amount'];
            return $user;
        })
            ->sortBy('full_name')
            ->values();

        $users = new EloquentCollection($users);

        if ($head) {
            $users = $users->prepend($head);
        }

        return $users;
    }

    public function userMonthReport(User $user, Carbon $date)
    {
        $days = DateHelper::daysInMonth($date);

        $days = $days->map(function ($day) use ($user) {
            return $this->userDayReport($user, $day);
        });

        return $days;
    }

    private function userDayReport(User $user, $day): array
    {
        $workingDayInfo = $this->service->countWorkHours($user, Carbon::parse($day['date']));
        $day['hours'] = $workingDayInfo['totalHours'];
        $day['timeCheckHours'] = $workingDayInfo['timeCheckHours'];
        $day['status'] = $workingDayInfo['status'];

        $day['statuses'] = $user->dailyWorkStatuses->filter(function ($status) use ($day) {
            if (in_array($status->workStatus->type, WorkStatus::EXCLUDE_TYPES)) {
                if ($status->status != DailyWorkStatus::STATUS_APPROVED) {
                    return false;
                }
            }
            if ($status->workStatus->type == WorkStatus::TYPE_PART_TIME_DAY) {
                if ($status->hours > $day['timeCheckHours'] && $day['timeCheckHours'] != null) {
                    return false;
                }
            }
            return $status->date->isSameDay($day['date']);
        })->values();

        $day['isLate'] = $day['statuses']->contains(function ($status) use ($day) {
            return $status->workStatus->type == WorkStatus::TYPE_LATE;
        });

        $day['isWorkingDay'] = DateHelper::isWorkingDay($day['date']);

        return $day;
    }

    private function loadRelation($users, Carbon $date): bool
    {
        $dateStart = $date->copy()->startOfMonth();
        $dateEnd = $date->copy()->endOfMonth();

        $users->loadMissing(
            [
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

        return false;
    }
}
