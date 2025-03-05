<?php

namespace App\Services\TimeCheckServices;

use App\Models\Department;
use App\Models\Option;
use App\Models\TimeCheck;
use App\Models\User; 
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(
        protected WorkTimeService $workTimeService,
    ){}

    public function getCurrentWorkTimeReport()
    {
        $report = Department::with([
            'users' => function ($query) {
                // Добавить проверку на уволенного сотрудника

                $query->with(['lastAction' => function ($query) {
                    $query->whereDate('date', Carbon::now());
                }]);
            },
            'childDepartments',
            'childDepartments.users' => function ($query) {

                // TODO
                // Добавить проверку на уволенного сотрудника
                // $query->where('created_at', '<=', $date);

                $query->with(['lastAction' => function ($query) {
                    $query->whereDate('date', Carbon::now());
                }]);
            },
        ])->whereNull('parent_id')->get();

        return $report;
    }

    public function getWorkTimeDayReport($date)
    {
        $report = Department::with([
            'users' => function ($query) use ($date) {
                // Добавить проверку на уволенного сотрудника

                $query->with(['timeChecks' => function ($query) use ($date) {
                    $query->whereDate('date', $date);
                }]);
            },
            'childDepartments',
            'childDepartments.users' => function ($query) use ($date) {

                // TODO
                // Добавить проверку на уволенного сотрудника
                // $query->where('created_at', '<=', $date);

                $query->with(['timeChecks' => function ($query) use ($date) {
                    $query->whereDate('date', $date);
                }]);
            },
        ])->whereNull('parent_id')->get();

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

}