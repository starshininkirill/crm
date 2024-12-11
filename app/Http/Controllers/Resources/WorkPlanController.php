<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkPlanRequest;
use App\Models\WorkPlan;

class WorkPlanController extends Controller
{

    public function store(WorkPlanRequest $request)
    {
        $validated = $request->validated();

        WorkPlan::create($validated);

        return redirect()->back()->with('success', 'План успешно создан');
    }


    public function update(WorkPlanRequest $request, WorkPlan $workPlan)
    {

        if ($workPlan->type == WorkPlan::B1_PLAN || $workPlan->type == WorkPlan::B2_PLAN) {
            $data = $request->updateData();
            if(array_key_exists('bonus', $data)){
                WorkPlan::where('department_id', $workPlan->department->id)
                ->where('type', $workPlan->type)
                ->whereYear('created_at', $workPlan->created_at->year)
                ->whereMonth('created_at', $workPlan->created_at->month) 
                ->update(['bonus' => $data['bonus']]);
            }
        }


        $validated = $request->updateData();
        
        $workPlan->update($validated);

        return redirect()->back()->with('success', 'План успешно изменён');
    }

    public function destroy(WorkPlan $workPlan)
    {
        $workPlan->delete();

        return redirect()->back()->with('success', 'План успешно удалён');
    }
}
