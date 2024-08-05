<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController
{
    public function index(){

        $users = User::all();

        return view('admin.user.index', ['users' => $users]);
    }
}
