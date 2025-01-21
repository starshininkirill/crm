<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {

        $departments = Department::getMainDepartments();

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
        return view('admin.department.create');
    }

    public function show(Department $department)
    {
        return Inertia::render('Admin/Department/Show', [
            'department' => $department,
        ]);
        return view('admin.department.show', ['department' => $department]);
    }
}
