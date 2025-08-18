<?php

namespace App\Services\ProjectReports\DTO;

use App\Models\UserManagement\Department;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportDataDTO
{
    public function __construct(
        public readonly Collection $upsails,
        public readonly Department $department,
        public readonly Collection $users,
        public readonly Collection $closeContracts,
        public readonly Collection $accountSeceivable,
        public readonly Collection $workPlans,
        public readonly Collection $otherAccountSeceivable,
        public readonly Carbon $date,
    ) {}

}
