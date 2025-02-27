<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Requests\Admin\UserRequest;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\Position;
use App\Models\User;
use Inertia\Inertia;

class UserController
{

    public function index()
    {

        $users = User::with('position')->get();

        $users = $users->map(function ($user) {
            return array_merge(
                $user->toArray(),
                ['position' => $user->position ? $user->position->name : null]
            );
        });

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $departments = Department::mainDepartments()->load('positions');
        $positions = Position::all();
        $employmentTypes = EmploymentType::all();

        return Inertia::render('Admin/User/Create', [
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

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        User::create($validatedData);

        return redirect()->back()->with('success', 'Сотрудник успешно создан');
    }
}
