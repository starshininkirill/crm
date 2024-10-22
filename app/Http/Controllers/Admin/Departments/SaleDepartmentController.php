<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\SaleDepartmentServices\PlansService;
use App\Services\SaleDepartmentServices\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleDepartmentController extends Controller
{
    protected $plansService;

    public function __construct(PlansService $plansService)
    {
        $this->plansService = $plansService;
    }


    public function index()
    {
        $department = Department::getMainSaleDepartment();
        return view('admin.departments.sale.index', ['department' => $department]);
    }

    public function userReport(Request $request)
    {

        $users = Department::getMainSaleDepartment()->activeUsers();

        $requestData = $request->only(['user', 'date']);

        if ($request->filled(['user', 'date'])) {
            $date = new Carbon($requestData['date']);
            $user = User::find($requestData['user']);

            $reportService = new ReportService($this->plansService, $date);

            $daylyReport = $reportService->mounthByDayReport($user);
            $motivationReport = $reportService->motivationReport($user);

            $pivotWeeks = $reportService->pivotWeek();
            $pivotDaily = $reportService->mounthByDayReport();

            $users = Department::getMainSaleDepartment()->activeUsers();
            $pivotUsers = $reportService->pivotUsers($users);

            $generalPlan = $reportService->generalPlan($pivotUsers);
        }
        return view(
            'admin.departments.sale.report',
            [
                'users' => $users,
                'user' => isset($user) ? $user : null,
                'date' => isset($date) ? $date : null,
                'daylyReport' => isset($daylyReport) ? $daylyReport : collect(),
                'motivationReport' => isset($motivationReport) ? $motivationReport : collect(),
                'pivotWeeks' => isset($pivotWeeks) ? $pivotWeeks : collect(),
                'pivotDaily' => isset($pivotDaily) ? $pivotDaily : collect(),
                'pivotUsers' => isset($pivotUsers) ? $pivotUsers : collect(),
                'generalPlan' => isset($generalPlan) ? $generalPlan : collect(),
                'serviceCategoryModel' => ServiceCategory::class
            ]
        );
    }
    public function reportSettings()
    {
        $departmentId = Department::getMainSaleDepartment()->id;
        $plans = WorkPlan::where('department_id', $departmentId)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->whereNotNull('mounth')
            ->orderBy('mounth')
            ->get();
        return view('admin.departments.sale.reportSettings', [
            'plans' => $plans
        ]);
    }
}

// $queryCount = 0;
// DB::listen(function ($query) use (&$queryCount) {
//     $queryCount++;
// });
// $start = microtime(true);
// $end = microtime(true);
// dd($end - $start);
// dd($queryCount);