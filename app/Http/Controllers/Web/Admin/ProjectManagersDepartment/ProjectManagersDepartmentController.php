<?php

namespace App\Http\Controllers\Web\Admin\ProjectManagersDepartment;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Services\ProjectReports\Generators\DepartmentReportGenerator;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $user = $request->get('user') ? User::find($request->get('user')) : null;

        $date = Carbon::parse($request->get('date')) ?? Carbon::now();
        $date = $date->endOfMonth();

        $allUsers = $department->allUsers($date, ['departmentHead'])->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        })->values();

        $report = $reportService->generateFullReport($department, $date);

        return Inertia::render('Admin/ProjectsDepartment/Report', [
            'date' => fn() => $date ? $date->format('Y-m') : now()->format('Y-m'),
            'users' => fn() => $allUsers,
            'selectUser' => fn() => $user ?? null,
            'report' => fn() => $report ?? [],
        ]);
    }
}
