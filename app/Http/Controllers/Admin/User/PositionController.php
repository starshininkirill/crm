<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PositionRequest;
use App\Models\Department;
use App\Models\Position;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index()
    {
        $departments = Department::mainDepartments();
        $positions = Position::get();

        return Inertia::render('Admin/User/Position/Index', [
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
