<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(){

        $users = User::all();

        return view('admin.user.index', ['users' => $users]);
    }

    public function create(){
        return view('admin.user.create');
    }

    public function store(UserRegisterRequest $request)
    {
        $validatedData = $request->validated();

        $this->userService->createUser($validatedData);

        return redirect()->back()->with('success', 'Сотрудник успешно создан');
    }
}