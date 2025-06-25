<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\DailyWorkStatus;
use App\Models\Department;
use App\Models\User;
use App\Models\WorkStatus;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use App\Services\SaleReports\Generators\DepartmentReportGenerator;
use App\Services\TimeCheckServices\WorkTimeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class TimeSheetService
{
    public function __construct(
        private ReportDTOBuilder $reportDTOBuilder,
        private WorkTimeService $service,
        private DepartmentReportGenerator $serviceReportGenerator,
    ) {}

    public function newGenerateUsersReport(Collection $departments, Carbon $date)
    {
        $departments->each(function ($department) use ($date) {
            $findUsersDate = DateHelper::isCurrentMonth($date) ? null : $date;
            $users = $department->allUsers($findUsersDate, ['departmentHead', 'position'])->filter(function ($user) {
                return $user->departmentHead->isEmpty();
            });

            $reportData = $this->reportDTOBuilder->buildFullReport($date, $department);

            $pivotUsers = $this->serviceReportGenerator->pivotUsers($reportData, $users);
            
        });
    }

    public function generateUsersReport(Collection $departmentsWithUsers, Carbon $date)
    {
        // dd($departmentsWithUsers);

        $usersReport = $departmentsWithUsers->map(function ($users, $departmentId) use ($date) {
            $this->loadRelation($users, $date);

            $this->calculateSalary($users, $departmentId, $date);

            $usersReport = $users->map(function ($user) use ($date) {
                $user['days'] = $this->userMonthReport($user, $date);
                $user['salary'] = $user->getSalary();
                $user['part_salary'] = $user['salary'] / 2;
                $user['hour_salary'] = round($user['salary'] / count(DateHelper::getWorkingDaysInMonth($date)) / 9);
                return $user;
            });

            return $usersReport;
        });

        return $usersReport;
    }

    private function calculateSalary(Collection $users, int|string $departmentId, Carbon $date)
    {
        if ($departmentId) {
            $department = $users[0]->department;

            if ($department->type == Department::SALE_DEPARTMENT) {
                $this->calculateSaleDepartmentSalary();
            }
        }

        return [
            'first_bonus' => 0,
            'amount_first_salary' => 0,
            'second_bonus' => 0,
            'amount_second_salary' => 0,
        ];
    }

    private function calculateSaleDepartmentSalary() {}

    public function userMonthReport(User $user, Carbon $date)
    {
        $days = DateHelper::daysInMonth($date);

        $this->loadRelation($user, $date);

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
        $needsLoad = false;

        if ($users instanceof User) {
            $needsLoad = !$users->relationLoaded('dailyWorkStatuses');
            $users = collect([$users]);
        } elseif ($users instanceof EloquentCollection) {
            $needsLoad = $users->contains(function ($user) {
                return !$user->relationLoaded('dailyWorkStatuses');
            });
        } else {
            return false;
        }

        if ($needsLoad) {
            $dateStart = $date->copy()->startOfMonth();
            $dateEnd = $date->copy()->endOfMonth();

            $users->load(['dailyWorkStatuses' => function ($query) use ($dateStart, $dateEnd) {
                $query->whereBetween('date', [$dateStart, $dateEnd])
                    ->with(['workStatus']);
            }]);

            return true;
        }

        return false;
    }
}
