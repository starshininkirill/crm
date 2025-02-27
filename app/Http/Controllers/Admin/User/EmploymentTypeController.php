<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Requests\Admin\EmploymentTypeRequest;
use App\Models\EmploymentType;
use Inertia\Inertia;

class EmploymentTypeController
{

    public function index()
    {
        $employmentTypes = EmploymentType::all();

        return Inertia::render('Admin/User/EmploymentType/Index', [
            'employmentTypes' => $employmentTypes,
        ]);
    }

    public function store(EmploymentTypeRequest $request)
    {
        $validated = $request->validated();

        EmploymentType::create($validated);

        return redirect()->back()->with('success', 'Тип устройства успешно создан');
    }

    public function destroy(EmploymentType $employmentType)
    {
        $employmentType->delete();

        return redirect()->back()->with('success', 'Тип устройства успешно удалён');
    }
}
