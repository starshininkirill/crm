<?php

namespace App\Services\TimeCheckServices;

use App\Models\Department;
use App\Models\WorkStatus;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        protected WorkTimeService $workTimeService,
    ) {}

    public function getCurrentWorkTimeReport($date)
    {
        $report = $this->getTimeChecksInfo(['lastAction', 'dailyWorkStatuses'], $date);

        return $report;
    }

    public function getWorkTimeDayReport($date)
    {
        $report = $this->getTimeChecksInfo(['timeChecks'], $date);

        $report->each(function ($department) use ($date) {
            $this->processDepartmentUsers($department, $date);

            if ($department->childDepartments) {
                $department->childDepartments->each(function ($childDepartment) use ($date) {
                    $this->processDepartmentUsers($childDepartment, $date);
                });
            }
        });

        return $report;
    }

    public function getLogReport($date)
    {
        $report = $this->getTimeChecksInfo(['timeChecks'], $date);

        $report->each(function ($department) use ($date) {
            $this->processDepartmentUsersForLog($department, $date);

            if ($department->childDepartments) {
                $department->childDepartments->each(function ($childDepartment) use ($date) {
                    $this->processDepartmentUsersForLog($childDepartment, $date);
                });
            }
        });

        return $report;
    }

    private function getTimeChecksInfo(array $relations, string|null $date = null)
    {
        $date = $date ?? Carbon::now();

        return Department::with([
            'users' => function ($query) use ($relations, $date) {
                if (!empty($relations)) {
                    foreach ($relations as $relation) {
                        $query->with([$relation => function ($query) use ($date, $relation) {
                            $query->whereDate('date', $date);
                            if ($relation == 'dailyWorkStatuses') {
                                $query->whereHas('workStatus', function ($query) {
                                    $query->whereNotIn('type', WorkStatus::EXCLUDE_TYPES);
                                });
                            }
                        }]);
                    }
                }
            },
            'childDepartments',
            'childDepartments.users' => function ($query) use ($relations, $date) {
                if (!empty($relations)) {
                    foreach ($relations as $relation) {
                        $query->with([$relation => function ($query) use ($date, $relation) {
                            $query->whereDate('date', $date);
                            if ($relation == 'dailyWorkStatuses') {
                                $query->whereHas('workStatus', function ($query) {
                                    $query->whereNotIn('type', WorkStatus::EXCLUDE_TYPES);
                                });
                            }
                        }]);
                    }
                }
            },
        ])->whereNull('parent_id')->get();
    }

    private function processDepartmentUsers(Department $department, $date)
    {
        $department->users->each(function ($user) use ($date) {
            $timeChecks = $user->timeChecks;

            $actionStart = $timeChecks->firstWhere('action', 'start');
            $actionEnd = $timeChecks->firstWhere('action', 'end');

            $breaktime = $this->workTimeService->userBreaktime($user, Carbon::parse($date));

            $user->isLate = $this->workTimeService->isUserLate($actionStart);
            $user->isOvertime = $this->workTimeService->isBreakOvertime($breaktime);
            $user->actionStart = $actionStart ? $actionStart->date->format('H:i:s') : null;
            $user->actionEnd = $actionEnd ? $actionEnd->date->format('H:i:s') : null;
            $user->breaktime = $breaktime;
            $user->workTime = $this->workTimeService->userWorktime($actionStart, $actionEnd, $breaktime);
        });
    }

    private function processDepartmentUsersForLog(Department $department, $date)
    {
        $department->users->each(function ($user) use ($date) {

            $user->timeChecks->each(function ($timeCheck) {
                $timeCheck->formated_date = Carbon::parse($timeCheck->date)->format('d.m.y');
                $timeCheck->time = Carbon::parse($timeCheck->date)->format('H:i:s');
            });
        });
    }
}
