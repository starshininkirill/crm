<?php

namespace App\Http\Controllers\Web\Admin\AdvertisingDepartment;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdvertisingDepartment\AdvertisingWorkPlanRequest;
use App\Models\Global\WorkPlan;
use App\Models\UserManagement\Department;
use App\Services\AdvertisingReports\Generators\DepartmentReportGenerator;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdvertisingDepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/AdvertisingDepartment/Index', []);
    }

    public function report(DepartmentReportGenerator $reportGenerator)
    {

        $date = Carbon::now();
        $department = Department::query()
            ->whereNull('parent_id')
            ->whereType(Department::DEPARTMENT_ADVERTISING)
            ->get()
            ->first();

        $report = $reportGenerator->generateReport($date, $department);

        return Inertia::render('Admin/AdvertisingDepartment/Report', [
            'pairs' => $report['pairs'],
            'users_report' => $report['users_report']
        ]);
    }

    public function plansSettings(Request $request, WorkPlanService $workPlanService)
    {
        $requestDate = $request->query('date');
        $date = DateHelper::getValidatedDateOrNow($requestDate);
        $isCurrentMonth = DateHelper::isCurrentMonth($date);

        $department = Department::whereNull('parent_id')
            ->where('type', Department::DEPARTMENT_ADVERTISING)
            ->first();

        $plans = $workPlanService->plansForDepartment($date, $department);

        return Inertia::render('Admin/AdvertisingDepartment/PlansSettings', [
            'dateProp' => $date->format('Y-m'),
            'isCurrentMonth' => $isCurrentMonth,
            'plans' => $plans,
            'departmentId' => $department->id,
        ]);
    }

    public function storeWorkPlan(AdvertisingWorkPlanRequest $request)
    {
        $validated = $request->validated();

        $workPlan = WorkPlan::create($validated);

        if (!$workPlan) {
            return redirect()->back()->withErrors('Не удалось создать план');
        }

        return redirect()->back()->with('success', 'План успешно создан');
    }

    public function updateWorkPlan(AdvertisingWorkPlanRequest $request, WorkPlan $workPlan)
    {
        $validated = $request->validated();

        if (!$workPlan->update($validated)) {
            return redirect()->back()->withErrors('Не удалось обновить план');
        }

        return redirect()->back()->with('success', 'План успешно изменён');
    }

    public function destroyWorkPlan(WorkPlan $workPlan)
    {
        if (!$workPlan->delete()) {
            return redirect()->back()->withErrors('Не удалось Удалить план');
        }

        return redirect()->back()->with('success', 'План успешно удалён');
    }
}
