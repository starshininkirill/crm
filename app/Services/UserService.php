<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        $positon_id = isset($data['position_id']) ? $data['position_id'] : '';
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            // 'position_id' => $positon_id,
            'password' => Hash::make($data['password']),
        ]);
    }
}
