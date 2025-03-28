<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\WorkStatusRequest;
use App\Models\Department;
use App\Models\WorkStatus;
use App\Services\TimeCheckServices\ReportService;
use App\Services\UserServices\TimeSheetService;
use App\Services\WorkStatusService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TimeCheckController extends Controller
{
    public function index(Request $request, ReportService $servise)
    {
        $date = $request->get('date') ?? Carbon::now()->format('Y-m-d');
        $workStatuses = WorkStatus::mainStatuses()->get();

        $todayReport = $servise->getCurrentWorkTimeReport();
        $dateReport = $servise->getWorkTimeDayReport($date);
        $logReport = $servise->getLogReport($date);

        return Inertia::render('Admin/TimeCheck/Index', [
            'todayReport' => $todayReport,
            'dateReport' => $dateReport,
            'logReport' => $logReport,
            'date' => $date,
            'workStatuses' => $workStatuses,
        ]);
    }

    public function handleWorkStatus(WorkStatusRequest $request, WorkStatusService $service)
    {
        $validated = $request->validated();

        $service->handleChange($validated);

        return response()->json([
            'success' => true,
            'message' => 'Статус успешно обновлен!',
        ], 200);
    }

    public function timeSheet(Request $request, TimeSheetService $service)
    {
        $departments = Department::all();
        $targetDate = Carbon::now();

        $days = DateHelper::daysInMonth($targetDate);

        $department = Department::whereType(Department::SALE_DEPARTMENT)->whereNull('parent_id')->first();
        $users = $department->allUsers();

        $usersReport = $service->generateUsersReport($users, $targetDate);

        return Inertia::render('Admin/TimeCheck/TimeSheet/Index',[
            'days' => $days,
            'departments' => $departments,
            'date' => $targetDate,
            'usersReport' => $usersReport,
        ]);
    }

    public function overwork(Request $request)
    {
        return 'Переработки';
    }
}
