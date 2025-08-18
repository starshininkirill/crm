<?php

namespace App\Services\SaleReports\WorkPlans;

use App\Helpers\DateHelper;
use App\Models\UserManagement\Department;
use App\Models\Global\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkPlanService
{
    public function actualPlans(Carbon|null $date = null, Department $department, $relations = []): Collection
    {
        $mainDepartmentId = $department->id;

        if (!$date || DateHelper::isCurrentMonth($date)) {
            return WorkPlan::where('department_id', $mainDepartmentId)
                ->with($relations)
                ->get();
        }

        $allHistoricalPlans = WorkPlan::getLatestHistoricalRecords($date);

        return $allHistoricalPlans->filter(function ($plan) use ($mainDepartmentId) {
            return $plan->department_id == $mainDepartmentId;
        });
    }

    public function plansForDepartment(Carbon $date, Department $department, $relations = []): Collection
    {
        $plans = self::actualPlans($date, $department, $relations)->groupBy('type');

        if ($plans->has(WorkPlan::MOUNTH_PLAN)) {
            $plans[WorkPlan::MOUNTH_PLAN] = $plans[WorkPlan::MOUNTH_PLAN]->filter(function ($plan) {
                return array_key_exists('month', $plan->data) && $plan->data['month'] != null;
            });
        }

        if ($plans->has(WorkPlan::MOUNTH_PLAN)) {
            $plans[WorkPlan::MOUNTH_PLAN] = $plans[WorkPlan::MOUNTH_PLAN]->sortBy('data.month');
        }
        if ($plans->has(WorkPlan::PERCENT_LADDER)) {
            $plans[WorkPlan::PERCENT_LADDER] = $plans[WorkPlan::PERCENT_LADDER]->sortBy(function ($plan) {
                return $plan->data['bonus'];
            })->values();
        }

        return $plans;
    }
}
