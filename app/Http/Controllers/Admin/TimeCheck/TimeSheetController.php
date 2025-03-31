<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\TimeSheetRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\UserServices\TimeSheetService;
use Carbon\Carbon;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    public function index(TimeSheetRequest $request, TimeSheetService $service)
    {
        $departments = Department::all();

        $targetDate = null;
        $department = null;
        $days = [];
        $usersReport = [];

        if ($request->filled('date') && $request->filled('department_id')) {
            $targetDate = Carbon::parse($request->input('date'))->endOfMonth();

            $department = Department::findOrFail($request->input('department_id'));

            $days = DateHelper::daysInMonth($targetDate);

            $users = $department->allUsers($targetDate);
            
            $usersReport = $service->generateUsersReport($users, $targetDate);
        }

        return Inertia::render('Admin/TimeCheck/TimeSheet/Index', [
            'days' => $days,
            'departments' => $departments,
            'department' => $department ?? null,
            'date' => $targetDate?->format('Y-m') ?? Carbon::now()->format('Y-m'),
            'usersReport' => $usersReport,
        ]);
    }
}
