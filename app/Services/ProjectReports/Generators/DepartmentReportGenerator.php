<?php

namespace App\Services\ProjectReports\Generators;

use App\Models\UserManagement\Department;
use App\Models\Global\WorkPlan;
use App\Services\ProjectReports\Builders\ReportDataDTOBuilder;
use App\Services\ProjectReports\DTO\UserDataDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DepartmentReportGenerator
{
    public function __construct(
        private ReportDataDTOBuilder $reportDataDTOBuilder,
    ) {}

    public function generateFullReport(Department $department, Carbon $date, bool $withOtherPayments = true): Collection
    {
        $fullReportData = $this->reportDataDTOBuilder->buildFullReport($date, $department);

        $users = $fullReportData->users->filter(function ($user) {
            return $user->departmentHead->isEmpty();
        });

        $report = $users->map(function ($user) use ($fullReportData) {
            $userData = $this->reportDataDTOBuilder->getUserSubdata($fullReportData, $user);

            return $this->processUser($userData);
        });

        if ($withOtherPayments) {
            $report->push($this->createOtherPaymentsRow($fullReportData->otherAccountSeceivable));
        }

        return $report;
    }

    protected function processUser(UserDataDTO $userData): Collection
    {
        $user = $userData->user;
        $user->is_probation = $userData->isProbation;

        $upsellsBonus = $this->calculateUpsalesBonus($userData);
        $b1PlanResult = $this->calculateB1Plan($userData);
        $b2PlanResult = $this->calculateB2Plan($userData);
        $b3PlanResult = $this->calculateB3Plan($userData);
        $b4PlanResult = $this->calculateB4Plan($userData);

        $percentLadder = $this->calculatePercentLadder($userData);

        $accountsReceivablePercent = $userData->accountSeceivable->sum('value') / 100 * $percentLadder;

        $totalBonuses = $upsellsBonus;

        if (!$user->is_probation) {
            $totalBonuses += $b1PlanResult['bonus'] +
            $b2PlanResult['bonus'] +
            $b3PlanResult['bonus'] +
            $b4PlanResult['bonus'] +
            $accountsReceivablePercent;
        }


        $user->bonuses = $totalBonuses;

        return collect([
            'user' => $user,
            'close_contracts' => $userData->closeContracts,
            'close_contracts_count' => $userData->closeContracts->count(),
            'close_contracts_sum' => $userData->closeContracts->sum('value'),
            'accounts_receivable' => $userData->accountSeceivable,
            'accounts_receivable_sum' => $userData->accountSeceivable->sum('value'),
            'accounts_receivable_percent' => $accountsReceivablePercent,
            'percent_ladder' => $percentLadder,
            'upsells' => $userData->upsails,
            'upsells_money' => $userData->upsailsMoney,
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

    protected function createOtherPaymentsRow(Collection $otherPayments): Collection
    {
        $sum = $otherPayments->sum('value');

        return collect([
            'user' => ['full_name' => 'GRAMPUS', 'is_probation' => true],
            'close_contracts' => collect(),
            'close_contracts_count' => 0,
            'close_contracts_sum' => 0,
            'accounts_receivable' => $otherPayments,
            'accounts_receivable_sum' => $sum,
            'accounts_receivable_percent' => 0,
            'percent_ladder' => 0,
            'upsells' => collect(),
            'upsells_money' => 0,
            'upsells_bonus' => 0,
            'compexes' => 0,
            'individual_sites' => 0,
            'ready_sites' => 0,
            'b1' => ['completed' => false, 'bonus' => 0],
            'b2' => ['completed' => false, 'bonus' => 0],
            'b3' => ['completed' => false, 'bonus' => 0],
            'b4' => ['completed' => false, 'bonus' => 0],
            'bonuses' => 0,
        ]);
    }

    protected function calculatePercentLadder(UserDataDTO $userData): int|float
    {
        $closedContractsCount = $userData->closeContracts->count();

        $ladderPlans = $userData->workPlans->where('type', WorkPlan::PERCENT_LADDER);

        $highestTierPlan = $ladderPlans->first(function ($plan) {
            return ! isset($plan->data['goal']);
        });

        $tieredPlans = $ladderPlans->filter(function ($plan) {
            return isset($plan->data['goal']);
        })->sortBy('data.goal');


        foreach ($tieredPlans as $plan) {
            if ($closedContractsCount < $plan->data['goal']) {
                return $plan->data['bonus'];
            }
        }

        if ($highestTierPlan) {
            return $highestTierPlan->data['bonus'];
        }

        return 0;
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

    protected function calculateB4Plan(UserDataDTO $userData): array
    {
        return $this->calculatePlan(
            $userData,
            WorkPlan::B4_PLAN,
            $userData->compexes
        );
    }

    protected function calculatePlan(UserDataDTO $userData, string $planType, int|float $currentValue): array
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
