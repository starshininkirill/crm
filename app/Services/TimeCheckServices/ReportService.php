<?php

namespace App\Services\TimeCheckServices;

use App\Models\DailyWorkStatus;
use App\Models\Department;
use App\Models\WorkStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    public function __construct(
        protected WorkTimeService $workTimeService,
    ) {}

    public function getWorkTimeDayReport($date)
    {
        $report = $this->getTimeChecksInfo(['timeChecks', 'lastAction', 'dailyWorkStatuses', 'lateWorkStatuses'], $date);

        $report->each(function ($department) use ($date) {
            $this->processDepartmentUsers($department, $date);

            if ($department->childDepartments) {
                $department->childDepartments->each(function ($childDepartment) use ($date) {
                    $this->processDepartmentUsers($childDepartment, $date);
                });
            }
        });

        return [
            'detailed' => $report,
            'aggregated' => $this->calculateAggregatedData($report),
        ];
    }

    public function calculateAggregatedData(Collection $report): array
    {
        $aggregated = [];
        $totals = ['work' => 0, 'onBreak' => 0, 'notWork' => 0, 'totalUsers' => 0];

        foreach ($report as $department) {
            $this->aggregateDepartment($department, $aggregated, $totals);

            if ($department->childDepartments->isNotEmpty()) {
                foreach ($department->childDepartments as $child) {
                    $this->aggregateDepartment($child, $aggregated, $totals);
                }
            }
        }

        $aggregated['Тотал'] = [
            'work' => $totals['work'] . ' / ' . $totals['totalUsers'],
            'onBreak' => $totals['onBreak'] . ' / ' . $totals['work'],
            'notWork' => $totals['notWork'],
        ];

        return $aggregated;
    }

    protected function aggregateDepartment(Department $department, array &$aggregated, array &$totals): void
    {
        $working = 0;
        $onBreak = 0;
        $finished = 0;
        $totalInDept = $department->users->count();

        foreach ($department->users as $user) {
            $hasStart = $user->timeChecks->where('action', 'start')->isNotEmpty();
            $hasEnd = $user->timeChecks->where('action', 'end')->isNotEmpty();

            $latestTimeCheck = $user->timeChecks->sortByDesc('id')->first();
            $isOnBreak = $latestTimeCheck?->action === 'pause';

            if ($hasEnd || $user->timeChecks->isEmpty()) {
                $finished++;
            } elseif ($hasStart) {
                $working++;
                if ($isOnBreak) {
                    $onBreak++;
                }
            }
        }

        if ($totalInDept > 0) {
            $aggregated[$department->name] = [
                'work' => $working . ' / ' . $totalInDept,
                'onBreak' => $onBreak . ' / ' . $working,
                'notWork' => $finished,
                'isMain' => $department->parent == null ? true :false,
            ];
        } else {
            $aggregated[$department->name] = [
                'work' => '',
                'onBreak' => '',
                'notWork' => '',
                'isMain' => $department->parent == null ? true :false,
            ];
        }

        $totals['work'] += $working;
        $totals['onBreak'] += $onBreak;
        $totals['notWork'] += $finished;
        $totals['totalUsers'] += $totalInDept;
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

    private function getTimeChecksInfo(array $relations, ?string $date = null): Collection
    {
        $date = $date ?? Carbon::now();

        return Department::with([
            'users' => fn($query) => $this->loadUserRelations($query, $relations, $date),
            'childDepartments',
            'childDepartments.users' => fn($query) => $this->loadUserRelations($query, $relations, $date),
        ])->whereNull('parent_id')->get();
    }

    private function loadUserRelations($query, array $relations, string $date): void
    {
        if (empty($relations)) {
            return;
        }

        foreach ($relations as $relation) {
            $query->with([
                $relation => function ($query) use ($date, $relation) {
                    $query->whereDate('date', $date);
                    if ($relation == 'dailyWorkStatuses') {
                        $query->whereHas('workStatus', function ($query) {
                            $query->whereNotIn('type', WorkStatus::EXCLUDE_TYPES);
                        });
                    }
                }
            ]);
        }
    }

    private function processDepartmentUsers(Department $department, $date)
    {
        $department->users->each(function ($user) use ($date) {
            $timeChecks = $user->timeChecks;

            $actionStart = $timeChecks->firstWhere('action', 'start');
            $actionEnd = $timeChecks->firstWhere('action', 'end');

            $breaktime = $this->workTimeService->userBreaktime($user, Carbon::parse($date));

            $user->isLate = $this->workTimeService->isUserLate($user, $date);
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
