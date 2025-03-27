<?php

namespace App\Http\Controllers\Admin\User;

use App\Helpers\DateHelper;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\Position;
use App\Models\User;
use App\Services\UserServices\TimeSheetService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Inertia\Inertia;

class UserController
{

    public function index(UserRequest $request)
    {

        $departmentId = $request->get('department'); // Получаем значение department из запроса

        $users = User::with('position', 'department')
            ->when($departmentId, function ($query, $departmentId) {
                // Если departmentId передан, добавляем условие фильтрации
                return $query->where('department_id', $departmentId);
            })
            ->get()
            ->map(function ($user) {
                $user->formatted_created_at = $user->created_at->format('d.m.Y');
                return $user;
            });

        $departments = Department::mainDepartments()->load('positions');
        $positions = Position::all();
        $employmentTypes = EmploymentType::all();

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'positions' => $positions,
            'departments' => $departments,
            'employmentTypes' => $employmentTypes,
        ]);
    }

    public function timeSheet(UserRequest $request, TimeSheetService $service)
    {
        $departments = Department::all();
        $targetDate = Carbon::now();

        $days = DateHelper::daysInMonth($targetDate);

        $department = Department::whereType(Department::SALE_DEPARTMENT)->whereNull('parent_id')->first();
        $users = $department->allUsers();

        $usersReport = $service->generateUsersReport($users, $targetDate);

        return Inertia::render('Admin/User/TimeSheet/Index',[
            'days' => $days,
            'departments' => $departments,
            'date' => $targetDate,
            'usersReport' => $usersReport,
        ]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/User/Show', [
            'user' => $user,
        ]);
    }

    public function store(UserRequest $request, UserService $service)
    {
        $validated = $request->validated();

        $user = $service->createEmployment($validated);

        return redirect()->back()->with('success', 'Сотрудник успешно создан');
    }
}
