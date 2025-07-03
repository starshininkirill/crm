<?php

namespace App\Services\ProjectReports\DTO;

use App\Models\Department;
use Illuminate\Support\Collection;

class ReportDataDTO
{
    public function __construct(
        public Collection $upsails = new Collection(),
        public Department $department,
        public Collection $users = new Collection(),
        public Collection $closeContracts = new Collection(),
        public Collection $accountSeceivable = new Collection(),
        public Collection $workPlans = new Collection(),
    ) {}
}
