<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PositionRequest;
use App\Models\Position;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Department/Position/Create');
    }

    public function store(PositionRequest $request)
    {
        $validatedData = $request->validated();

        Position::create($validatedData);

        return redirect()->route('admin.department.position.create')->with('success', 'Должность успешно создана.');
    }
}
