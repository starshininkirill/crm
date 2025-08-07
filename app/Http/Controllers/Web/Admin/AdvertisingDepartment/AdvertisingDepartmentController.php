<?php

namespace App\Http\Controllers\Web\Admin\AdvertisingDepartment;

use App\Http\Controllers\Controller;
use App\Models\UserManagement\Department;
use App\Services\AdvertisingReports\Generators\DepartmentReportGenerator;
use Carbon\Carbon;
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
}
