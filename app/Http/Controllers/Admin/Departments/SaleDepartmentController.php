<?php

namespace App\Http\Controllers\Admin\Departments;

use App\Http\Controllers\Controller;
use App\Models\Departments\SaleDepartment;
use App\Models\User;
use App\Services\SaleDepartmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaleDepartmentController extends Controller
{

    protected $saleDepartmentService;

    public function __construct(
        SaleDepartmentService $saleDepartmentService,
    ) {
        $this->saleDepartmentService = $saleDepartmentService;
    }


    public function index()
    {
        $department = SaleDepartment::getMainDepartment();
        return view('admin.departments.sale.index', ['department' => $department]);
    }

    public function userReport(Request $request)
    {
        $users = SaleDepartment::getMainDepartment()->activeUsers();
        $date = null;
        $user = null;
        $report = null;

        $requestData = $request->only(['user', 'date']);

        if ($request->filled(['user', 'date'])) {
            $date = new Carbon($requestData['date']);
            $user = User::find($requestData['user']);
            $report = $this->saleDepartmentService->generateUserReportData($date, $user);
        }
        return view(
            'admin.departments.sale.userReport',
            [
                'users' => $users,
                'user' => $user,
                'report' => $report,
                'date' => $date
            ]
        );
    }
}
