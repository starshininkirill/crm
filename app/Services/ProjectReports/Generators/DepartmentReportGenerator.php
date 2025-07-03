<?php

namespace App\Services\ProjectReports\Generators;

use App\Models\Department;
use App\Services\ProjectReports\Builders\ReportDataDTOBuilder;
use App\Services\ProjectReports\DTO\UserDataDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DepartmentReportGenerator
{
    public function __construct(
        private ReportDataDTOBuilder $reportDataDTOBuilder,
    ) {}

    public function generateFullReport(Department $department, Carbon $date): Collection
    {
        $fullReportData = $this->reportDataDTOBuilder->buildFullReport($date, $department);

        $users = $fullReportData->users;

        return $users->map(function ($user) use ($fullReportData) {
            $userData = $this->reportDataDTOBuilder->getUserSubdata($fullReportData, $user);

            return $this->processUser($userData);
        });
    }

    protected function processUser(UserDataDTO $userData): Collection
    {
        $user = $userData->user;

        // TODO: percent_upsells - поменять на план
        return collect([
            'user' => $user->only('id', 'full_name'),
            'accounts_receivable' => $userData->accountSeceivable->sum('value'),
            'percent_ladder' => 1.5,
            'upsells' => $userData->upsailsMoney,
            'percent_upsells' => $userData->upsailsMoney * 0.1,
            'compexes' => $userData->compexes,
            'individual_sites' => $userData->individualSites,
            'ready_sites' => $userData->readySites,
            'b1' => 0,
            'b2' => 0,
            'b3' => 0,
            'b4' => 0,
        ]);
    }
}
