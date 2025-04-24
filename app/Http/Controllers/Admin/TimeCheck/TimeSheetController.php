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
        $department = $request->filled('department_id')
            ? Department::findOrFail($request->input('department_id'))
            : null;
    
        $targetDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->endOfMonth()
            : Carbon::now()->endOfMonth();
    
        $users = $department
            ? $department->allUsers($targetDate)
            : User::all();
    
        return Inertia::render('Admin/TimeCheck/TimeSheet/Index', [
            'days' => DateHelper::daysInMonth($targetDate),
            'departments' => Department::all(),
            'department' => $department,
            'date' => $targetDate->format('Y-m'),
            'usersReport' => $service->generateUsersReport($users, $targetDate),
        ]);
    }
}
