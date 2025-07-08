<?php

namespace App\Http\Controllers\Web\Admin\ProjectManagersDepartment;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectManagersDepartment\ProjectWorkPlanRequest;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\ProjectReports\Generators\DepartmentReportGenerator;
use App\Services\ProjectReports\Generators\HeadReportGenerator;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProjectManagersDepartmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProjectsDepartment/Index', []);
    }

    public function report(Request $request, DepartmentReportGenerator $reportService)
    {
        $department = Department::firstWhere('type', Department::DEPARTMENT_PROJECT_MANAGERS);

        $date = Carbon::parse($request->get('date')) ?? Carbon::now();
        $date = $date->endOfMonth();

        $allUsers = $department->allUsers($date, ['departmentHead'])->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        })->values();

        $report = $reportService->generateFullReport($department, $date);

        return Inertia::render('Admin/ProjectsDepartment/Report', [
            'date' => fn() => $date ? $date->format('Y-m') : now()->format('Y-m'),
            'users' => fn() => $allUsers,
            'report' => fn() => $report ?? [],
        ]);
    }

    public function headReport(Request $request, HeadReportGenerator $headReportGenerator)
    {

        $date = Carbon::parse($request->get('date')) ?? Carbon::now();
        $date = $date->endOfMonth();

        $report = $headReportGenerator->generateHeadReport( $date);

        return Inertia::render('Admin/ProjectsDepartment/HeadReport', [
            'date' => fn() => $date ? $date->format('Y-m') : now()->format('Y-m'),
            'report' => fn() => $report ?? [],
        ]);
    }

    public function plansSettings(Request $request, WorkPlanService $workPlanService)
    {
        $requestDate = $request->query('date');
        $date = DateHelper::getValidatedDateOrNow($requestDate);
        $isCurrentMonth = DateHelper::isCurrentMonth($date);

        $department = Department::firstWhere('type', Department::DEPARTMENT_PROJECT_MANAGERS);

        $plans = $workPlanService->plansForDepartment($date, $department);

        $serviceCats = ServiceCategory::all();

        return Inertia::render('Admin/ProjectsDepartment/PlansSettings', [
            'dateProp' => $date->format('Y-m'),
            'serviceCats' => $serviceCats,
            'isCurrentMonth' => $isCurrentMonth,
            'plans' => $plans,
            'departmentId' => $department->id,
        ]);
    }

    public function storeWorkPlan(ProjectWorkPlanRequest $request)
    {
        $validated = $request->validated();

        $workPlan = WorkPlan::create($validated);
        
        if (!$workPlan) {
            return redirect()->back()->withErrors('Не удалось создать план');
        }
        
        return redirect()->back()->with('success', 'План успешно создан');
    }
    
    public function updateWorkPlan(ProjectWorkPlanRequest $request, WorkPlan $workPlan)
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
