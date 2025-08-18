<?php

namespace App\Services\AdvertisingReports\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportDataDTO
{
    public function __construct(
        public Carbon $date,
        public Collection $activeContracts,
        public Collection $allServiceMonths,
        public Collection $previousMonthlyServices,
        public Collection $nextMonthlyServices,
        public Collection $pairs,
        public Collection $users,
        public Collection $plans,
        public Collection $upsails,
    ) {}

}
