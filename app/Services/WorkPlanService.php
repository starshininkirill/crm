<?php

namespace App\Services;

use App\Models\WorkPlan;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Bool_;

class WorkPlanService
{

    public function store(array|Collection $data): ?WorkPlan
    {
        $workPlan = WorkPlan::create($data);

        return $workPlan;
    }

    public function update(WorkPlan $workPlan, array|Collection $data): bool
    {
        return $workPlan->update($data);
    }

    public function destroy(WorkPlan $workPlan): bool
    {
        return $workPlan->delete();
    }
}
