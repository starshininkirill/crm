<?php

namespace App\Services\UserServices;

use App\Helpers\DateHelper;
use App\Models\TimeCheck;
use App\Models\User;
use App\Models\WorkStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'surname' => $data['surname'],
                'bitrix_id' => $data['bitrix_id'],
                'work_phone' => $data['work_phone'],
                'email' => $data['email'],
                'password' => $data['password'],
                'department_id' => $data['department_id'],
                'position_id' => $data['position_id'],
            ];

            if (array_key_exists('probation_dates', $data) && $data['probation_dates'] != null && count($data['probation_dates']) == 2) {
                $userData['probation_start'] = Carbon::parse($data['probation_dates'][0]);
                $userData['probation_end'] = Carbon::parse($data['probation_dates'][1]);
            }

            if (array_key_exists('personal_salary', $data) && $data['personal_salary'] != null) {
                $userData['salary'] = $data['personal_salary'];
            }

            $user = User::create($userData);

            $user->employmentDetail()->create([
                'employment_type_id' => $data['employment_type_id'],
                'details' => $data['details'],
                'payment_account' => $data['payment_account'],
            ]);

            return $user;
        });
    }

    public function filterUsersByStatus(Collection $users, string $status, Carbon $targetDate): Collection
    {
        $endOfMonth = $targetDate->copy()->endOfMonth();
        $startOfMonth = $targetDate->copy()->startOfMonth();

        return $users->filter(function ($user) use ($status, $startOfMonth, $endOfMonth) {
            $firedAt = $user->fired_at;

            return match ($status) {
                'active' => $firedAt === null || ($firedAt >= $startOfMonth && $firedAt <= $endOfMonth),
                'fired' => $firedAt !== null && $firedAt <= $endOfMonth,
                'all' => true,
                default => $firedAt === null || $firedAt > $endOfMonth,
            };
        });
    }
}
