<?php

namespace App\Http\Controllers\Admin\TimeCheck;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TimeCheck\TimeSheetRequest;
use App\Models\Department;
use App\Models\User;
use App\Services\UserServices\TimeSheetService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class TimeSheetController extends Controller
{
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

        $users = $this->filterUsersByStatus($users, $status, $targetDate);


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
                ? $department->allUsers($targetDate)->loadMissing($relations)
                : $department->allHistoryUsers($targetDate, $relations);
        }

        return $isCurrentMonth
            ? User::with($relations)->get()
            : User::getLatestHistoricalRecords($targetDate, $relations);
    }

    protected function filterUsersByStatus(Collection $users, string $status, Carbon $targetDate): Collection
    {
        $endOfMonth = $targetDate->copy()->endOfMonth();
        $startOfMonth = $targetDate->copy()->startOfMonth();

        return $users->filter(function ($user) use ($status, $startOfMonth, $endOfMonth) {
            $firedAt = $user->fired_at;

            return match ($status) {
                'active' => $firedAt === null || ($firedAt >= $startOfMonth && $firedAt <= $endOfMonth),
                'fired' => $firedAt !== null && $firedAt <= $endOfMonth,
                'all' => true,
                default => $firedAt === null || $firedAt > $endOfMonth,
            };
        });
    }
}
