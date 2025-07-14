<?php

namespace App\Http\Controllers\Web\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PositionRequest;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\Position;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index()
    {
        $departments = Department::mainDepartments();
        $positions = Position::get();

        return Inertia::render('Admin/Staff/Position/Index', [
            'departments' => $departments,
            'positions' => $positions,
        ]);
    } 

    public function store(PositionRequest $request)
    {
        $validatedData = $request->validated();

        Position::create($validatedData);

        return redirect()->back()->with('success', 'Должность успешно создана.');
    }
}
