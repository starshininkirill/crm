<?php

namespace App\Http\Controllers\Admin\User;

use App\Exceptions\Business\BusinessException;
use App\Http\Filters\Models\UserFilter;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\Position;
use App\Models\User;
use App\Services\UserServices\UserService;
use Inertia\Inertia;

class UserController
{
    public function index(UserRequest $request, UserFilter $filter)
    {
        $users = User::with('position', 'department')
            ->filter($filter)
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'position' => $user->position,
                    'department' => $user->department,
                    'email' => $user->email,
                    'formatted_created_at' => $user->created_at->format('d.m.Y'),
                    'fired_at' => $user->fired_at,
                ];
            });

        $departments = Department::all();
        $positions = Position::all();
        $employmentTypes = EmploymentType::all();

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'positions' => $positions,
            'departments' => $departments,
            'employmentTypes' => $employmentTypes,
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

    public function fire(User $user)
    {
        if (!$user->fire()) {
            throw new BusinessException('Не удалось уволить сотрудника');
        }

        return redirect()->back()->with('success', 'Сотрудник уволен');
    }
}
