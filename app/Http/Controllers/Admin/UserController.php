<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\Position;
use App\Models\User;
use Inertia\Inertia;

class UserController
{

    public function index()
    {

        $users = User::with('position')->get();

        $users = $users->map(function ($user) {
            return array_merge(
                $user->toArray(),
                ['position' => $user->position ? $user->position->name : null]
            );
        });

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        $positions = Position::all();

        return Inertia::render('Admin/User/Create');

        return view('admin.user.create', ['positions' => $positions]);
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/User/Show', [
            'user' => $user,
        ]);
    }

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        User::create($validatedData);

        return redirect()->back()->with('success', 'Сотрудник успешно создан');
    }
}
