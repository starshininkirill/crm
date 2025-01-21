<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        User::create($validatedData);

        return redirect()->back()->with('success', 'Сотрудник успешно создан');
    }
}
