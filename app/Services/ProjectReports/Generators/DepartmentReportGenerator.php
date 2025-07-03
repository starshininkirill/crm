<?php

namespace App\Services\ProjectReports\Generators;

use App\Models\Department;
use App\Models\WorkPlan;
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
            'compexes' => $this->calculateCompexes($userData),
            'individual_sites' => $this->calculateIndividualSites($userData),
            'ready_sites' => $this->calculateReadySites($userData),
            'b1' => 0,
            'b2' => 0,
            'b3' => 0,
            'b4' => 0,
        ]);
    }

    protected function calculateIndividualSites(UserDataDTO $userData): int
    {
        return $this->calculateContractsByWorkPlanType($userData, WorkPlan::INDIVID_CATEGORY_IDS);
    }

    protected function calculateReadySites(UserDataDTO $userData): int
    {
        return $this->calculateContractsByWorkPlanType($userData, WorkPlan::READY_SYTES_CATEGORY_IDS);
    }

    private function calculateContractsByWorkPlanType(UserDataDTO $userData, string $workPlanType): int
    {
        $categoryIds = $userData->workPlans
            ->where('type', $workPlanType)
            ->pluck('data.categoryIds')
            ->flatten()
            ->filter();

        if ($categoryIds->isEmpty()) {
            return 0;
        }

        $contracts = $userData->closeContracts->filter(function ($contract) use ($categoryIds) {
            return $contract->services->pluck('category.id')->intersect($categoryIds)->isNotEmpty();
        });

        return $contracts->count();
    }

    protected function calculateCompexes(UserDataDTO $userData): int
    {
        $contracts = $userData->closeContracts->where('is_complex', true);

        return $contracts->count();
    }
}
