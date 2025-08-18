<?php

namespace App\Services\ProjectReports\DTO;

use App\Models\UserManagement\Department;
use App\Models\UserManagement\User;
use Illuminate\Support\Collection;

class UserDataDTO
{
    public function __construct(
        public Collection $upsails = new Collection(),
        public float $upsailsMoney = 0,
        public Department $department,
        public User $user,
        public Collection $closeContracts = new Collection(),
        public Collection $accountSeceivable = new Collection(),
        public Collection $workPlans = new Collection(),
        public int $individualSites = 0,
        public int $readySites = 0,
        public int $compexes = 0,
        public bool $isProbation = false,
    ) {}
}
