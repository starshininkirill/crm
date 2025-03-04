<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {

        $departments = Department::mainDepartments();

        $departments = $departments->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                'childsDepartments' => $department->childDepartments
            ];
        });

        return Inertia::render('Admin/Department/Index', [
            'departments' => $departments,
        ]);

        return view('admin.department.index', ['departments' => $departments]);
    }

    public function create()
    {
        return Inertia::render('Admin/Department/Create');
    }

    public function show(Department $department)
    {

        $users = $department->allUsers()->map(function ($user) {
            return [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'position' => $user->position,
            ];
        });

        return Inertia::render('Admin/Department/Show', [
            'department' => $department,
            'parent' => $department->parent,
            'childDepartments' => $department->childDepartments,
            'users' => $users,
        ]);
    }
    public function store(DepartmentRequest $request)
    {
        $validated = $request->validated();

        Department::create($validated);

        return redirect()->back()->with('success', 'Отдел успешно создан');
    }
}
