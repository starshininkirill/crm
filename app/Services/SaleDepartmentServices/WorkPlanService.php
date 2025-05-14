<?php

namespace App\Services\SaleDepartmentServices;

use App\Helpers\DateHelper;
use App\Models\Department;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WorkPlanService
{
    public function actualSalePlans(Carbon|null $date = null, array $relations = []) : Collection
    {
        $mainDepartmentId = Department::getMainSaleDepartment()->id;

        if (!$date || DateHelper::isCurrentMonth($date)) {
            return WorkPlan::where('department_id', $mainDepartmentId)
                ->with($relations)
                ->get();
        }

        $allHistoricalPlans = WorkPlan::getLatestHistoricalRecords($date, $relations);

        return $allHistoricalPlans->filter(function ($plan) use ($mainDepartmentId) {
            return $plan->department_id == $mainDepartmentId;
        });
    }

    public function plansForSaleSettings(Carbon $date): Collection
    {
        $plans = self::actualSalePlans($date)->groupBy('type');

        if ($plans->has(WorkPlan::MOUNTH_PLAN)) {
            $plans[WorkPlan::MOUNTH_PLAN] = $plans[WorkPlan::MOUNTH_PLAN]->filter(function ($plan) {
                return array_key_exists('month', $plan->data) && $plan->data['month'] != null;
            });
        }

        if ($plans->has(WorkPlan::MOUNTH_PLAN)) {
            $plans[WorkPlan::MOUNTH_PLAN]->sortBy('data.month');
        }
        if ($plans->has(WorkPlan::PERCENT_LADDER)) {
            $plans[WorkPlan::PERCENT_LADDER]->sortBy('data.goal');
        }

        return $plans;
    }
}
