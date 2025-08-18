<?php

namespace App\Services\AdvertisingReports\DTO;

use App\Models\UserManagement\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserDataDTO
{
    public function __construct(
        public User $user,
        public Carbon $date,
        public Collection $allServiceMonths,
        public Collection $previousMonthlyServices,
        public Collection $nextMonthlyServices,
        public int $newTarifCount,
        public Collection $pairs,
        public Collection $workPlans,
        public Collection $upsails,
    ) {}

}
