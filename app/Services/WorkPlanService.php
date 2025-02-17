<?php

namespace App\Services;

use App\Models\WorkPlan;
use Illuminate\Support\Collection;

class WorkPlanService
{

    public function store(array|Collection $data): ?WorkPlan
    {
        $workPlan = WorkPlan::create($data);

        return $workPlan;
    }

    public function update(WorkPlan $workPlan, array|Collection $data): ?WorkPlan
    {
        $workPlan->update($data);

        return $workPlan;
    }

    public function destroy(WorkPlan $workPlan)
    {
        $workPlan->delete();
    }
}
