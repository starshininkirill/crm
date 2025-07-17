<?php

namespace App\Services\SaleReports\Plans;

use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\Services\ServiceCategory;
use App\Models\Global\WorkPlan;
use App\Models\UserManagement\Position;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\DTO\UserDataDTO;
use App\Services\UserServices\UserService;
use Illuminate\Support\Collection;

abstract class AbstractDepartmentPlanCalculator
{
    protected $boneses = 0;

    public function __construct(
        protected UserService $userService
    ) {}


    abstract public function b2Plan(UserDataDTO $reportInfo): Collection;

    abstract public function b3Plan(UserDataDTO $reportInfo): Collection;

    abstract public function b4Plan(UserDataDTO $reportInfo): Collection;

    abstract protected function getLadderPlans(UserDataDTO $reportInfo): Collection;

    protected function getPlan(string|int $planType, ReportDTO|UserDataDTO $reportInfo): ?WorkPlan
    {
        return $reportInfo->workPlans->where('type', $planType)
            ->whereNull('position_id')
            ->first();
    }

    public function b1Plan(UserDataDTO $reportInfo): Collection
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

    public function getBonuses()
    {
        $bonuses = $this->boneses;

        $this->boneses = 0;

        return $bonuses;
    }

    protected function updateBonus(int|float $bonus): void
    {
        $this->boneses += $bonus;
    }

    public function monthPlan(UserDataDTO|ReportDTO $reportInfo): Collection
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

    public function weeksPlan(ReportDTO|UserDataDTO $reportInfo): Collection
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

    public function weeksReport(ReportDTO|UserDataDTO $reportInfo): Collection
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

    public function totalValues(ReportDTO|UserDataDTO $reportInfo): Collection
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


    protected function calculateWeeksCompleted(Collection $weeks, WorkPlan $plan): Collection
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

    public function calculateSalary(Collection $report, ReportDTO|UserDataDTO $reportInfo, float|int $bonuses): Collection
    {
        $bonusPercentage = $this->calculateBonusPercentage($reportInfo);

        $res = collect([
            'bonuses' => $bonuses,
            'newMoney' => 0,
            'oldMoney' => 0,
            'amount' => $bonuses,
            'percentage' => $bonusPercentage,
        ]);

        $res['newMoney'] = ($reportInfo->newMoney * $bonusPercentage) / 100;
        $res['oldMoney'] = ($reportInfo->oldMoney * $bonusPercentage) / 100;
        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];

        if (!$report['b1']['completed']) {
            $b1Plan = $this->getPlan(WorkPlan::B1_PLAN, $reportInfo);
            if ($b1Plan && isset($b1Plan->data['bonus'])) {
                $penaltyPercentage = $b1Plan->data['bonus'];
                $res['newMoney'] -= ($res['newMoney'] * ($penaltyPercentage / 100));
                $res['oldMoney'] -= ($res['oldMoney'] * ($penaltyPercentage / 100));
                $res['bonuses'] -= ($res['bonuses'] * ($penaltyPercentage / 100));
            }
        }

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];

        return $res;
    }

    private function calculateBonusPercentage(UserDataDTO $reportInfo): float
    {
        $allLadderPlans = $this->getLadderPlans($reportInfo);

        if ($allLadderPlans->isEmpty()) {
            return 0.0;
        }

        $topTierPlan = $allLadderPlans->firstWhere('data.goal', null);
        $tieredPlans = $allLadderPlans->whereNotNull('data.goal')->sortBy('data.goal');

        $bonusPercentage = 0.0;

        $achievedPlan = $tieredPlans
            ->where('data.goal', '<=', $reportInfo->newMoney)
            ->last();

        if ($achievedPlan) {
            $bonusPercentage = (float) $achievedPlan->data['bonus'];

            $highestTierWithGoal = $tieredPlans->last();
            if ($topTierPlan && $reportInfo->newMoney > $highestTierWithGoal->data['goal']) {
                $bonusPercentage = (float) $topTierPlan->data['bonus'];
            }
        }

        $monthWorked = $this->userService->getMonthWorked($reportInfo->user, $reportInfo->date);
        $gracePeriodPlan = $reportInfo->workPlans
            ->where('type', WorkPlan::NO_PERCENTAGE_MONTH)
            ->first();

        if ($gracePeriodPlan && $monthWorked <= $gracePeriodPlan->data['goal'] && $bonusPercentage === 0.0) {
            $minimalBonusPlan = $tieredPlans
                ->where('data.bonus', '>', 0)
                ->sortBy('data.bonus')
                ->first();

            if ($minimalBonusPlan) {
                return (float) $minimalBonusPlan->data['bonus'];
            }
        }

        return $bonusPercentage;
    }

    protected function checkPreviousWeek(Collection &$weeksCompleted, int $currentKey, float $currentWeekMoney, float $planGoal): void
    {
        $previousWeek = $weeksCompleted[$currentKey - 1];
        if (!$previousWeek['completed'] && $currentWeekMoney >= $planGoal / 2) {
            $weeksCompleted[$currentKey - 1]['completed'] = true;
        }
    }
}
