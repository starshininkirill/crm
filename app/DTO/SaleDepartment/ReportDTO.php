<?php

namespace App\DTO\SaleDepartment;

use App\Models\Department;
use App\Models\User;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportDTO
{
    public function __construct(
        public float $newMoney = 0,
        public float $oldMoney = 0,
        public ?WorkPlan $monthWorkPlan = null,
        public float $monthWorkPlanGoal = 0,
        public Collection $workingDays = new Collection(),
        public Collection $payments = new Collection(),
        public Collection $newPayments = new Collection(),
        public Collection $oldPayments = new Collection(),
        public Collection $contracts = new Collection(),
        public int $mainDepartmentId = 0,
        public ?Department $department = null,
        public ?Carbon $date = null,
        public ?User $user = null,
        public Collection $servicesByCatsCount = new Collection(),
        public float $bonuses = 0,
        public Collection $workPlans = new Collection(),
        public bool $isUserData = false,
        public Collection $callsStat = new Collection(),
        public Collection $usedPayments = new Collection(),
    ) {}
}
