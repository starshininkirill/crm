<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function createEmployment(array $data) :User
    {
        return  DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'salary' => $data['salary'],
                'probation' => $data['probation'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
            ]);

            $user->employmentDetail()->create([
                'employment_type_id' => $data['employment_type_id'],
                'details' => $data['details'],
            ]);

            return $user;
        });


    }
}
