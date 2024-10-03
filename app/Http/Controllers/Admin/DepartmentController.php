<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(){

        $departments = Department::getMainDepartments();

        return view('admin.department.index', ['departments' => $departments]);
    }

    public function create(){
        return view('admin.department.create');
    }

    public function store(DepartmentRequest $request){
        $validated = $request->validated();

        Department::create($validated);

        return redirect()->back()->with('success', 'Отдел успешно создан');
    }

    public function show(Department $department)
    {
        return view('admin.department.show', ['department' => $department]);
    }
}
 