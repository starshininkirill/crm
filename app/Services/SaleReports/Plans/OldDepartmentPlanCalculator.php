<?php

namespace App\Services\SaleReports\Plans;

use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\Services\ServiceCategory;
use App\Models\Global\WorkPlan;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\DTO\UserDataDTO;
use App\Services\UserServices\UserService;
use Illuminate\Support\Collection;

class DefaultSalePlanCalculator
{
    private $boneses = 0;

    public function __construct(
        private UserService $userService
    ) {}
 
    public function getBonuses()
    {
        $bonuses = $this->boneses;

        $this->boneses = 0;
        return $bonuses;
    }

    private function getPlan(string|int $planType, UserDataDTO $reportInfo): ?WorkPlan
    {
        return $reportInfo->workPlans->firstWhere('type', $planType);
    }

    private function updateBonus(int|float $bonus): void
    {
        $this->boneses += $bonus;
    }

    public function monthPlan(UserDataDTO $reportInfo): Collection
    {
        return  collect(
            [
                'goal' => $reportInfo->monthWorkPlanGoal,
                'value' => $reportInfo->newMoney,
                'completed' => $reportInfo->newMoney >= $reportInfo->monthWorkPlanGoal ? true : false,
            ]
        );
    }

    public function doublePlan(UserDataDTO $reportInfo): Collection
    {

        $res = collect(
            [
                'goal' => $reportInfo->monthWorkPlanGoal * 2,
                'value' => $reportInfo->newMoney,
                'completed' => false,
                'bonus' => 0
            ]
        );

        $completed = $reportInfo->newMoney >= $reportInfo->monthWorkPlanGoal * 2 ? true : false;

        if ($completed) {
            $res['completed'] = true;

            $planInstance = $this->getPlan(WorkPlan::DOUBLE_PLAN, $reportInfo);

            if ($planInstance) {
                $res['bonus'] = $planInstance->data['bonus'];
                $this->updateBonus($planInstance->data['bonus']);
            }
        }

        return $res;
    }

    public function bonusPlan(UserDataDTO $reportInfo): Collection
    {
        $res = collect(
            [
                'value' => $reportInfo->newMoney,
                'completed' => false,
                'bonus' => 0,
            ]
        );

        $planInstance = $this->getPlan(WorkPlan::BONUS_PLAN, $reportInfo);

        if ($planInstance) {
            $planGoal = $planInstance->data['goal'];

            $res['goal'] = $planGoal;

            if ($reportInfo->newMoney >= $planGoal && $planGoal != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->data['bonus'];
                $this->updateBonus($planInstance->data['bonus']);
            }
        }

        return $res;
    }

    public function weeksPlan(UserDataDTO $reportInfo): Collection
    {
        $res = collect();
        $weekPlan = $reportInfo->monthWorkPlanGoal / 4;
        $weeks = $reportInfo->financeWeeks;
        $trackedContractsIds = collect();

        foreach ($weeks as $week) {

            $newFilteredPayments = $reportInfo->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFiltredContractsIds = $newFilteredPayments->pluck('contract_id')->unique();

            $newUniqueContractsIds = $newFiltredContractsIds->diff($trackedContractsIds);

            if ($trackedContractsIds->isEmpty()) {
                $trackedContractsIds = $newFiltredContractsIds;
            } else {
                $trackedContractsIds = $trackedContractsIds->merge($newUniqueContractsIds);
            }

            $newUniqueContracts = $reportInfo->contracts->whereIn('id', $newUniqueContractsIds);

            $oldFilteredPayments = $reportInfo->oldPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFilteredPaymentsSum = $newFilteredPayments->sum('value');
            $oldFilteredPaymentsSum = $oldFilteredPayments->sum('value');


            $weekResult = collect([
                'start' => $week['date_start']->format('d'),
                'end' => $week['date_end']->format('d'),
                'goal' => $weekPlan,
                'newMoney' => $newFilteredPaymentsSum,
                'oldMoney' => $oldFilteredPaymentsSum,
                'completed' => false,
                'bonus' => 0
            ]);

            $servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($newUniqueContracts);
            $weekResult['servicesByCatsCount'] = $servicesByCatsCount;
            if ($newFilteredPaymentsSum >= $weekPlan) {
                $weekResult['completed'] = true;
                $weekPlanInstance = $this->getPlan(WorkPlan::WEEK_PLAN, $reportInfo);
                if ($weekPlanInstance != null) {
                    $weekResult['bonus'] = $weekPlanInstance->data['bonus'];
                    $this->updateBonus($weekPlanInstance->data['bonus']);
                }
            }
            $res[] = $weekResult;
        };
        return $res;
    }

    public function weeksReport(UserDataDTO|ReportDTO $reportInfo): Collection
    {
        $res = collect();
        $weeks = DateHelper::splitMonthIntoWeek($reportInfo->date);
        $trackedContractsIds = collect();

        foreach ($weeks as $week) {

            $newFilteredPayments = $reportInfo->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFiltredContractsIds = $newFilteredPayments->pluck('contract_id')->unique();

            $newUniqueContractsIds = $newFiltredContractsIds->diff($trackedContractsIds);

            if ($trackedContractsIds->isEmpty()) {
                $trackedContractsIds = $newFiltredContractsIds;
            } else {
                $trackedContractsIds = $trackedContractsIds->merge($newUniqueContractsIds);
            }

            $newUniqueContracts = $reportInfo->contracts->whereIn('id', $newUniqueContractsIds);

            $oldFilteredPayments = $reportInfo->oldPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFilteredPaymentsSum = $newFilteredPayments->sum('value');
            $oldFilteredPaymentsSum = $oldFilteredPayments->sum('value');


            $weekResult = collect([
                'start' => $week['date_start']->format('d'),
                'end' => $week['date_end']->format('d'),
                'newMoney' => $newFilteredPaymentsSum,
                'oldMoney' => $oldFilteredPaymentsSum,
            ]);

            $servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($newUniqueContracts);
            $weekResult['servicesByCatsCount'] = $servicesByCatsCount;
            $res[] = $weekResult;
        };
        return $res;
    }

    public function totalValues(UserDataDTO|ReportDTO $reportInfo): Collection
    {
        $res = collect([
            'newMoney' => $reportInfo->newMoney,
            'oldMoney' => $reportInfo->oldMoney,
            'servicesByCatsCount' => [
                ServiceCategory::INDIVIDUAL_SITE => $reportInfo->servicesByCatsCount[ServiceCategory::INDIVIDUAL_SITE],
                ServiceCategory::READY_SITE => $reportInfo->servicesByCatsCount[ServiceCategory::READY_SITE],
                ServiceCategory::RK => $reportInfo->servicesByCatsCount[ServiceCategory::RK],
                ServiceCategory::SEO => $reportInfo->servicesByCatsCount[ServiceCategory::SEO],
                ServiceCategory::OTHER => $reportInfo->servicesByCatsCount[ServiceCategory::OTHER],
            ]
        ]);

        return $res;
    }

    public function b1Plan(UserDataDTO $reportInfo)
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B1_PLAN, $reportInfo);

        if (!$plan) {
            return $result;
        }

        $averageDuration = $reportInfo->callsStat->map(function ($call) {
            return $call->duration / 60;
        })->avg();

        $averageCalls = $reportInfo->callsStat->map(function ($call) {
            return $call->income + $call->outcome;
        })->avg();

        if ($averageDuration >= $plan->data['avgDurationCalls'] && $averageCalls >= $plan->data['avgCountCalls']) {
            $result['completed'] = true;
            $result['bonus'] = $plan->data['bonus'];
        }

        if ($reportInfo->newMoney >= $plan->data['goal']) {
            $result['completed'] = true;
            $result['bonus'] = $plan->data['bonus'];
        }

        return $result;
    }

    public function b2Plan(UserDataDTO $reportInfo)
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B2_PLAN, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $excludedServiceIds = $plan->data['excludeIds'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds, $excludedServiceIds) {
                $serviceIds = $contract->services->pluck('id');
                if ($serviceIds->intersect($includedServiceIds)->isEmpty()) {
                    return false;
                }

                if (!$serviceIds->intersect($excludedServiceIds)->isEmpty()) {
                    return false;
                }

                return true;
            })
            ->count();

        if ($matchingContractCount >= $goal) {
            $result['completed'] = true;
            $result['bonus'] = $bonus;
            $this->updateBonus($bonus);
        }

        return $result;
    }

    public function b3Plan(UserDataDTO $reportInfo)
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B3_PLAN, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $includedServiceCategoriesIds = $plan->data['includedCategoryIds'] ?? [];
        $excludeServicePairs = $plan->data['excludeServicePairs'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds, $includedServiceCategoriesIds, $excludeServicePairs) {
                $serviceIds = $contract->services->pluck('id');

                if ($serviceIds->count() < 2) {
                    return false;
                }
                $servicesFromCategories = $contract->services->whereIn('service_category_id', $includedServiceCategoriesIds)->pluck('id');

                $hasIncludedService = $serviceIds->intersect($includedServiceIds)->isNotEmpty();

                $hasIncludedCategory = $servicesFromCategories->intersect($includedServiceCategoriesIds)->isNotEmpty();

                $hasValidServices = $hasIncludedService && $hasIncludedCategory;

                if (!$hasValidServices) {
                    return false;
                }

                foreach ($excludeServicePairs as $pair) {
                    if ($serviceIds->contains($pair[0]) && $serviceIds->contains($pair[1])) {
                        return false;
                    }
                }

                return true;
            })
            ->count();

        if ($matchingContractCount >= $goal) {
            $result['completed'] = true;
            $result['bonus'] = $bonus;
            $this->updateBonus($bonus);
        }

        return $result;
    }


    public function b4Plan(UserDataDTO $reportInfo)
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $plan = $this->getPlan(WorkPlan::B4_PLAN, $reportInfo);

        if (!$plan || empty($plan->data)) {
            return $result;
        }

        $includedServiceIds = $plan->data['includeIds'] ?? [];
        $goal = $plan->data['goal'] ?? '';
        $bonus = $plan->data['bonus'] ?? 0;

        if (empty($includedServiceIds) || !is_numeric($goal) || $goal <= 0) {
            return $result;
        }

        $matchingContractCount = $reportInfo->contracts
            ->filter(function ($contract) use ($includedServiceIds) {
                $serviceIds = $contract->services->pluck('id');
                return !$serviceIds->intersect($includedServiceIds)->isEmpty();
            })
            ->count();

        if ($matchingContractCount >= $goal) {
            $result['completed'] = true;
            $result['bonus'] = $bonus;
            $this->updateBonus($bonus);
        }

        return $result;
    }

    public function superPlan(Collection $weeks, UserDataDTO $reportInfo): Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
            'value' => $reportInfo->newMoney,
        ]);

        $plan = $this->getPlan(WorkPlan::SUPER_PLAN, $reportInfo);

        if (is_null($plan)) {
            return $result;
        }

        $result['goal'] = $plan->data['goal'];

        if ($reportInfo->newMoney >= $plan->data['goal']) {
            $result['completed'] = true;
            $result['bonus'] = $plan->data['bonus'];
            $this->updateBonus($plan->data['bonus']);
            return $result;
        }

        $weeksCompleted = $this->calculateWeeksCompleted($weeks, $reportInfo->monthWorkPlan);

        if (!$weeksCompleted->every(fn($weekStat) => $weekStat['completed'])) {
            return $result;
        }


        $result['completed'] = true;
        $result['bonus'] = $plan->data['bonus'];
        $this->updateBonus($plan->data['bonus']);

        return $result;
    }


    private function calculateWeeksCompleted(Collection $weeks, WorkPlan $plan): Collection
    {
        $weeksCompleted = collect();

        foreach ($weeks as $key => $week) {
            $weekStat = collect([
                'completed' => false,
                'order' => $key,
            ]);

            if ($week['newMoney'] >= $plan->data['goal'] / 4) {
                $weekStat['completed'] = true;
            }

            if ($key > 0) {
                $this->checkPreviousWeek($weeksCompleted, $key, $week['newMoney'], $plan->data['goal']);
            }

            $weeksCompleted->push($weekStat);
        }

        return $weeksCompleted;
    }

    public function calculateSalary(Collection $report, UserDataDTO $reportInfo, float|int $bonuses): Collection
    {
        $monthWorked = $this->userService->getMonthWorked($reportInfo->user, $reportInfo->date);
        $bonus = null;

        $res = collect([
            'bonuses' => $bonuses,
            'newMoney' => 0,
            'oldMoney' => 0,
            'amount' => $bonuses
        ]);

        $noPercentageMonth = $reportInfo->workPlans->where('type', WorkPlan::NO_PERCENTAGE_MONTH)->first();

        
        if ($noPercentageMonth && array_key_exists('goal', $noPercentageMonth->data) && $monthWorked > $noPercentageMonth->data['goal']) {
            $minimalWorkPlan = $reportInfo->workPlans->where('type', WorkPlan::PERCENT_LADDER)
                ->whereNotNull('data.goal')
                ->sortBy('data.goal')
                ->skip(1)
                ->first();
            if ($reportInfo->newMoney < $minimalWorkPlan->data['goal']) {
                $bonus = 0;
            }
        }

        $workPlan = $reportInfo->workPlans->where('data.goal', '>', $reportInfo->newMoney)
            ->where('type', WorkPlan::PERCENT_LADDER)
            ->where('department_id', $reportInfo->mainDepartmentId)
            ->sortBy('data.goal')
            ->first();

        if ($workPlan == null) {
            $workPlan = $reportInfo->workPlans->where('type', WorkPlan::PERCENT_LADDER)
                ->sortByDesc('data.bonus')
                ->first();
        }

        if ($workPlan == null) {
            return $res;
        }

        $bonus !== 0 ? $bonus = $workPlan->data['bonus'] : '';

        $res['percentage'] = $bonus;
        $res['newMoney'] = ($reportInfo->newMoney * $bonus) / 100;
        $res['oldMoney'] = ($reportInfo->oldMoney * $bonus) / 100;

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];


        if (!$report['b1']['completed']) {
            $b1Plan = $this->getPlan(WorkPlan::B1_PLAN, $reportInfo);
            $res['newMoney'] = $res['newMoney'] - ($res['newMoney'] * ($b1Plan->data['bonus'] / 100));
            $res['oldMoney'] = $res['oldMoney'] - ($res['oldMoney'] * ($b1Plan->data['bonus'] / 100));
            $res['bonuses'] = $res['bonuses'] - ($res['bonuses'] * ($b1Plan->data['bonus'] / 100));
        };

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];


        return $res;
    }

    private function checkPreviousWeek(Collection &$weeksCompleted, int $currentKey, float $currentWeekMoney, float $planGoal): void
    {
        $previousWeek = $weeksCompleted[$currentKey - 1];
        if (!$previousWeek['completed'] && $currentWeekMoney >= $planGoal / 2) {
            $weeksCompleted[$currentKey - 1]['completed'] = true;
        }
    }
}
