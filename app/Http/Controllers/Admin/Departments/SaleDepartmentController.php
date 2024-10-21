<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\SaleDepartmentService;
use App\Services\SaleDepartmentServices\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleDepartmentController extends Controller
{
    protected $saleDepartmentReportService;

    public function __construct(
        ReportService $saleDepartmentReportService,
    ) {
        $this->saleDepartmentReportService = $saleDepartmentReportService;
    }


    public function index()
    {
        $department = Department::getMainSaleDepartment();
        return view('admin.departments.sale.index', ['department' => $department]);
    }

    public function userReport(Request $request)
    {

        $users = Department::getMainSaleDepartment()->activeUsers();
        $date = null;
        $user = null;
        $daylyReport = collect();
        $motivationReport = collect();
        $pivotWeeks = collect();
        $pivotDaily = collect();
        $pivotUsers = collect();

        $requestData = $request->only(['user', 'date']);

        if ($request->filled(['user', 'date'])) {
            $date = new Carbon($requestData['date']);
            $user = User::find($requestData['user']);

            $queryCount = 0;
            DB::listen(function ($query) use (&$queryCount) {
                $queryCount++;
            });
            $start = microtime(true);



            $this->saleDepartmentReportService->prepareData($date);

            $daylyReport = $this->saleDepartmentReportService->mounthByDayReport($user);
            $motivationReport = $this->saleDepartmentReportService->motivationReport($user);

            $pivotWeeks = $this->saleDepartmentReportService->pivotWeek();
            $pivotDaily = $this->saleDepartmentReportService->mounthByDayReport();

            $users = Department::getMainSaleDepartment()->activeUsers();
            $pivotUsers = $this->saleDepartmentReportService->pivotUsers($users);


            // $end = microtime(true);
            // dd($end - $start);
            // dd($queryCount);
        }
        return view(
            'admin.departments.sale.userReport',
            [
                'users' => $users,
                'user' => $user,
                'daylyReport' => $daylyReport,
                'motivationReport' => $motivationReport,
                'pivotWeeks' => $pivotWeeks,
                'pivotDaily' => $pivotDaily,
                'serviceCategoryModel' => ServiceCategory::class,
                'date' => $date
            ]
        );
    }
}
