<?php

namespace App\Services\AdvertisingReports\Generators;

use App\Models\UserManagement\Department;
use App\Services\AdvertisingReports\Builders\ReportDataDTOBuilder;
use Carbon\Carbon;

class DepartmentReportGenerator
{

    public function __construct(
        private ReportDataDTOBuilder $reportDataDTOBuilder,
    ) {}

    public function generateReport(Carbon $date, Department $department)
    {
        $date = Carbon::now();

        $fullReportData = $this->reportDataDTOBuilder->prepareFullData($date, $department);

        $users = $fullReportData->users;

        $users = $users->map(function($user){
            $user->current_months_count = $user->current_months->count();
            $user->next_months_count = $user->next_months->count();

            $user->next_months_amount = $user->next_months->sum('price');
            $user->next_months_average = $user->next_months_amount / $user->next_months_count;

            return $user;
        });

        return [
            'pairs' => $fullReportData->pairs,
            'users_report' => $users,
        ];
    }
}
