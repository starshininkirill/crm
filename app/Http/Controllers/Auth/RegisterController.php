<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        
    }

    public function register(RegisterRequest $request)
    {   

        $validated = $request->validated();

        if($user = User::create($validated)){
            $token = $user->createToken('base')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 201);
        }
        
        return response()->json([
            'message' => 'Не удалось зарегистрировать пользователя'
        ], 500);
    }

}