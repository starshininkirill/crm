<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\SaleDepartmentReportService;
use App\Services\SaleDepartmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleDepartmentController extends Controller
{

    protected $saleDepartmentService;
    protected $saleDepartmentReportService;

    public function __construct(
        SaleDepartmentService $saleDepartmentService,
        SaleDepartmentReportService $saleDepartmentReportService,
    ) {
        $this->saleDepartmentService = $saleDepartmentService;
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

        $requestData = $request->only(['user', 'date']);

        if ($request->filled(['user', 'date'])) {
            $date = new Carbon($requestData['date']);
            $user = User::find($requestData['user']);
            $daylyReport = $this->saleDepartmentReportService->generateUserReportData($date, $user);
            $motivationReport = $this->saleDepartmentReportService->generateUserMotivationReportData($date, $user);
        }
        return view(
            'admin.departments.sale.userReport',
            [
                'users' => $users,
                'user' => $user,
                'daylyReport' => $daylyReport,
                'motivationReport' => $motivationReport,
                'serviceCategoryModel' => ServiceCategory::class,
                'date' => $date
            ]
        );
    }
}
 