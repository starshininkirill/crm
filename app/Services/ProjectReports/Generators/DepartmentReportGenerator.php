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

        $upsellsBonus = $this->calculateUpsalesBonus($userData);
        $b1PlanResult = $this->calculateB1Plan($userData);
        $b2PlanResult = $this->calculateB2Plan($userData);
        $b3PlanResult = $this->calculateB3Plan($userData);
        $b4PlanResult = ['bonus' => 0, 'completed' => false]; // Placeholder for B4

        $totalBonuses =
            $upsellsBonus +
            $b1PlanResult['bonus'] +
            $b2PlanResult['bonus'] +
            $b3PlanResult['bonus'] +
            $b4PlanResult['bonus'];

        return collect([
            'user' => $user->only('id', 'full_name'),
            'accounts_receivable' => $userData->accountSeceivable->sum('value'),
            'percent_ladder' => 1.5,
            'upsells' => $userData->upsailsMoney,
            'upsells_bonus' => $upsellsBonus,
            'compexes' => $userData->compexes,
            'individual_sites' => $userData->individualSites,
            'ready_sites' => $userData->readySites,
            'b1' => $b1PlanResult,
            'b2' => $b2PlanResult,
            'b3' => $b3PlanResult,
            'b4' => $b4PlanResult,
            'bonuses' => $totalBonuses,
        ]);
    }

    protected function calculateUpsalesBonus(UserDataDTO $userData): int|float
    {
        $upsalesBonusPlan = $userData->workPlans->where('type', WorkPlan::UPSALE_BONUS)->first();

        if (!$upsalesBonusPlan || ($upsalesBonusPlan->data['bonus'] ?? 0) == 0) {
            return 0;
        }

        return $userData->upsailsMoney / 100 * $upsalesBonusPlan->data['bonus'];
    }

    protected function calculateB1Plan(UserDataDTO $userData): array
    {
        return $this->calculatePlan(
            $userData,
            WorkPlan::B1_PLAN,
            $userData->individualSites
        );
    }

    protected function calculateB2Plan(UserDataDTO $userData): array
    {
        return $this->calculatePlan(
            $userData,
            WorkPlan::B2_PLAN,
            $userData->readySites
        );
    }

    protected function calculateB3Plan(UserDataDTO $userData): array
    {
        return $this->calculatePlan(
            $userData,
            WorkPlan::B3_PLAN,
            $userData->accountSeceivable->sum('value')
        );
    }

    private function calculatePlan(UserDataDTO $userData, string $planType, int|float $currentValue): array
    {
        $defaultResult = [
            'bonus' => 0,
            'completed' => false,
        ];

        $plan = $userData->workPlans->where('type', $planType)->first();

        if (!$plan) {
            return $defaultResult;
        }

        $goal = $plan->data['goal'] ?? 0;
        $bonus = $plan->data['bonus'] ?? 0;

        if ($goal <= 0) {
            return $defaultResult;
        }

        if ($currentValue >= $goal) {
            return [
                'bonus' => $bonus,
                'completed' => true,
            ];
        }

        return $defaultResult;
    }
}
