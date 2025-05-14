<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\TimeSheetRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\UserServices\TimeSheetService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index(TimeSheetRequest $request, TimeSheetService $service)
    {
        $status = $request->input('status', 'all');

        $department = $request->filled('department_id')
            ? Department::findOrFail($request->input('department_id'))
            : null;

        $targetDate = $request->filled('date')
            ? Carbon::parse($request->input('date'))->endOfMonth()
            : Carbon::now()->endOfMonth();

        $users = $this->getUsersCollection($department, $targetDate);

        $users = $this->userService->filterUsersByStatus($users, $status, $targetDate);


        $info = [
            'days' => DateHelper::daysInMonth($targetDate),
            'departments' => Department::all(),
            'department' => $department,
            'status' => $status,
            'date' => $targetDate->format('Y-m'),
            'usersReport' => collect(),
        ];
        if ($targetDate <= Carbon::now()->endOfMonth()) {
            $info['usersReport'] =  $service->generateUsersReport($users, $targetDate);
        }

        return Inertia::render('Admin/TimeCheck/TimeSheet/Index', $info);
    }

    protected function getUsersCollection(?Department $department, Carbon $targetDate): Collection
    {
        $isCurrentMonth = DateHelper::isCurrentMonth($targetDate);
        $relations = ['position'];

        if ($department) {
            return $isCurrentMonth
                ? $department->allUsers()->loadMissing($relations)
                : $department->allUsers($targetDate, $relations);
        }

        return $isCurrentMonth
            ? User::with($relations)->get()
            : User::getLatestHistoricalRecords($targetDate, $relations);
    }
}
