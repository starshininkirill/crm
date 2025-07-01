<?php

namespace App\Http\Controllers\Web\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\EmploymentTypeRequest;
use App\Models\EmploymentType;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;

class EmploymentTypeController extends Controller
{

    public function index()
    {
        $employmentTypes = EmploymentType::all();

        return Inertia::render('Admin/User/EmploymentType/Index', [
            'employmentTypes' => $employmentTypes,
        ]);
    }

    public function store(EmploymentTypeRequest $request): RedirectResponse
    {
        EmploymentType::create($request->validated());

        return redirect()->back()->with('success', 'Тип трудоустройства успешно создан');
    }

    public function destroy(EmploymentType $employmentType): RedirectResponse
    {
        $employmentType->delete();

        return redirect()->back()->with('success', 'Тип трудоустройства успешно удалён');
    }
}
