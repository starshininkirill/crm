<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\WorkStatusRequest;
use App\Models\DailyWorkStatus;
use App\Models\Department;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\ReportService;
use App\Services\UserServices\TimeSheetService;
use App\Services\WorkStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    public function index(Request $request, TimeSheetService $service)
    {
        $departments = Department::all();
        $targetDate = Carbon::now();

        $days = DateHelper::daysInMonth($targetDate);

        $department = Department::whereType(Department::SALE_DEPARTMENT)->whereNull('parent_id')->first();
        $users = $department->allUsers();

        $usersReport = $service->generateUsersReport($users, $targetDate);

        return Inertia::render('Admin/TimeCheck/TimeSheet/Index', [
            'days' => $days,
            'departments' => $departments,
            'date' => $targetDate,
            'usersReport' => $usersReport,
        ]);
    }
}
