<?php

namespace App\Services\ProjectReports\Generators;

use App\Models\Finance\HistoryReport;
use App\Models\UserManagement\Department;
use App\Models\Global\WorkPlan;
use App\Services\ProjectReports\Builders\ReportDataDTOBuilder;
use App\Services\ProjectReports\DTO\UserDataDTO;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DepartmentReportGenerator
{
    public function __construct(
        private ReportDataDTOBuilder $reportDataDTOBuilder,
    ) {}

    // Отчёт для отображения
    // Для отображения подходит любой тип данных (array or Obect)
    public function generateFullReport(Department $department, Carbon $date, bool $withOtherPayments = true): Collection
    {
        // $existingReport = $this->generateHistoryFullReport($date, $department, $withOtherPayments);

        // if ($existingReport) {
        //     return $existingReport;
        // }

        return $this->generateRowFullReport($date, $department, $withOtherPayments);
    }

    // Отчёт для расчёта
    // Для использования как основания для расчёта других отчётов
    public function generateRowFullReport(Carbon $date, Department $department, bool $withOtherPayments = true)
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

    // Отчёт только для отображения!!!
    // При использования как основания для расчёта других отчётов возникает ошибка Array not Obect!!!
    public function generateHistoryFullReport(Carbon $date, Department $department, bool $withOtherPayments = true)
    {
        if (!($date->year <= now()->year && $date->month < now()->month)) {
            return null;
        }

        $report = $this->getHistoryReport($date, $department);

        if ($report && !$report->isEmpty()) {
            $resultReport = $report;
        } else {
            $resultReport = $this->generateReportSnapshot($department, $date);
        }

        if (!$withOtherPayments) {
            $resultReport = $resultReport->filter(function ($userReport) {
                return array_key_exists('id', $userReport['user']);
            });
        }

        return $resultReport;
    }

    public function generateReportSnapshot(Department $department, Carbon $date, bool $withOtherPayments = true): Collection
    {
        $fullReportData = $this->reportDataDTOBuilder->buildFullReport($date, $department);
        $users = $fullReportData->users->filter(fn($user) => $user->departmentHead->isEmpty());

        $userReports = $users->map(function ($user) use ($fullReportData) {
            $userData = $this->reportDataDTOBuilder->getUserSubdata($fullReportData, $user);

            $upsellsBonus = $this->calculateUpsalesBonus($userData);
            $b1PlanResult = $this->calculateB1Plan($userData);
            $b2PlanResult = $this->calculateB2Plan($userData);
            $b3PlanResult = $this->calculateB3Plan($userData);
            $b4PlanResult = $this->calculateB4Plan($userData);
            $percentLadder = $this->calculatePercentLadder($userData);
            $accountsReceivablePercent = $userData->accountSeceivable->sum('value') / 100 * $percentLadder;

            $totalBonuses = $upsellsBonus;
            if (!$userData->isProbation) {
                $totalBonuses += $b1PlanResult['bonus'] +
                    $b2PlanResult['bonus'] +
                    $b3PlanResult['bonus'] +
                    $b4PlanResult['bonus'] +
                    $accountsReceivablePercent;
            }


            $user->is_probation = $userData->isProbation;

            return  [
                'user' => $user,
                'bonuses' => $totalBonuses,
                // Дополнительные итоговые данные для быстрого доступа
                'total_closed_contracts_sum' => $userData->closeContracts->sum('value'),
                'close_contracts' => $userData->closeContracts,
                'close_contracts_count' => $userData->closeContracts->count(),
                'close_contracts_sum' => $userData->accountSeceivable->sum('value'),
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

                'upsell_bonus' => [
                    'bonus' => $upsellsBonus,
                    'total_upsell_money' => $userData->upsailsMoney,
                    'bonus_percentage' => $userData->workPlans->where('type', WorkPlan::UPSALE_BONUS)->first()->data['bonus'] ?? 0,
                    'source_payment_ids' => $userData->upsails->pluck('id')->all(),
                ],
                'b1' => [
                    'bonus' => $b1PlanResult['bonus'],
                    'completed' => $b1PlanResult['completed'],
                    'current_value' => $userData->individualSites,
                    'goal' => $userData->workPlans->where('type', WorkPlan::B1_PLAN)->first()->data['goal'] ?? 0,
                    'source_contract_ids' => $userData->closeContracts
                        ->filter(function ($contract) use ($userData) {
                            $categoryIds = $userData->workPlans->where('type', WorkPlan::INDIVID_CATEGORY_IDS)->pluck('data.categoryIds')->flatten()->filter();
                            if ($categoryIds->isEmpty()) return false;
                            return $contract->services->pluck('category.id')->intersect($categoryIds)->isNotEmpty();
                        })
                        ->pluck('id')->all(),
                ],
                'b2' => [
                    'bonus' => $b2PlanResult['bonus'],
                    'completed' => $b2PlanResult['completed'],
                    'current_value' => $userData->readySites,
                    'goal' => $userData->workPlans->where('type', WorkPlan::B2_PLAN)->first()->data['goal'] ?? 0,
                    'source_contract_ids' => $userData->closeContracts
                        ->filter(function ($contract) use ($userData) {
                            $categoryIds = $userData->workPlans->where('type', WorkPlan::READY_SYTES_CATEGORY_IDS)->pluck('data.categoryIds')->flatten()->filter();
                            if ($categoryIds->isEmpty()) return false;
                            return $contract->services->pluck('category.id')->intersect($categoryIds)->isNotEmpty();
                        })
                        ->pluck('id')->all(),
                ],
                'b3' => [
                    'bonus' => $b3PlanResult['bonus'],
                    'completed' => $b3PlanResult['completed'],
                    'current_value' => $userData->accountSeceivable->sum('value'),
                    'goal' => $userData->workPlans->where('type', WorkPlan::B3_PLAN)->first()->data['goal'] ?? 0,
                    'source_payment_ids' => $userData->accountSeceivable->pluck('id')->all(),
                ],
                'b4' => [
                    'bonus' => $b4PlanResult['bonus'],
                    'completed' => $b4PlanResult['completed'],
                    'current_value' => $userData->compexes,
                    'goal' => $userData->workPlans->where('type', WorkPlan::B4_PLAN)->first()->data['goal'] ?? 0,
                    'source_contract_ids' => $userData->closeContracts
                        ->where('is_complex', true)
                        ->pluck('id')->all(),
                ],
                'percent_ladder' => [
                    'bonus' => $percentLadder,
                    'accounts_receivable_sum' => $userData->accountSeceivable->sum('value'),
                    'ladder_percent' => $percentLadder,
                    'source_payment_ids' => $userData->accountSeceivable->pluck('id')->all(),
                ],
            ];
        });

        $userReports->push($this->createOtherPaymentsRow($fullReportData->otherAccountSeceivable));

        $historyReport = HistoryReport::create([
            'version' => '1.1',
            'period' => $date,
            'department_id' => $department->id,
            'type' => HistoryReport::TYPE_PROJECTS_DEPARTMENT,
            'data' => [
                'user_reports' => $userReports->values()->all(),
                'other_payments' => [
                    'total' => $fullReportData->otherAccountSeceivable->sum('value'),
                    'source_payment_ids' => $fullReportData->otherAccountSeceivable->pluck('id')->all(),
                ],
            ]
        ]);

        return collect($historyReport->data['user_reports']);
    }

    protected function getHistoryReport(Carbon $date, Department $department): Collection
    {
        $historyReport = HistoryReport::where('type', HistoryReport::TYPE_PROJECTS_DEPARTMENT)
            ->whereYear('period', $date->year)
            ->whereMonth('period', $date->month)
            ->where('department_id', $department->id)
            ->first();

        if (!$historyReport) {
            return collect();
        }

        return collect($historyReport->data['user_reports']);
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
            'percent_ladder' => [
                'bonus' => $percentLadder,
            ],
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
            'percent_ladder' => [
                'bonus' => 0
            ],
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

        return  $userData->upsailsMoney / 100 * $upsalesBonusPlan->data['bonus'];
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
