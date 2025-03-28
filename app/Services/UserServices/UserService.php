<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\TimeCheck;
use App\Models\User;
use App\Models\WorkStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function getFirstWorkingDay(User $user): Carbon
    {
        $employmentDate = $user->created_at->copy();
        $workingDays = DateHelper::getWorkingDaysInMonth($employmentDate);

        while (!$workingDays->contains($employmentDate->format('Y-m-d'))) {
            $employmentDate->addDay(1, 'day');
        }

        return $employmentDate;
    }

    public function getMonthWorked(User $user, Carbon $date = null): int
    {
        $date = $date ?? Carbon::now();
        $employmentDate = $this->getFirstWorkingDay($user);

        if ($date->format('Y-m') == $employmentDate->format('Y-m')) {
            return 1;
        }

        $startWorkingDay = $employmentDate->format('d');

        $monthsWorked = $employmentDate->floorMonth()->diffInMonths($date->floorMonth()) + 1;
        if ($startWorkingDay > 7) {
            $monthsWorked--;
        }
        return $monthsWorked;
    }


    public function createEmployment(array $data): User
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
