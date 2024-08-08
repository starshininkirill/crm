<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PositionRequest;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function create(){
        $departments = Department::all();

        return view('admin.department.position.create', ['departments' => $departments]);
    }

    public function store(PositionRequest $request)
    {
        $validatedData = $request->validated();

        Position::create($validatedData);

        return redirect()->route('admin.department.position.create')->with('success', 'Должность успешно создана.');
    }
}
 