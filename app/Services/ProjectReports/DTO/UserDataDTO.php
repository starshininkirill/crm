<?php

namespace App\Services\ProjectReports\DTO;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Collection;

class UserDataDTO
{
    public function __construct(
        public Collection $upsails = new Collection(),
        public float $upsailsMoney = 0,
        public Department $department,
        public User $user,
    ) {}
}
