<?php

namespace App\Services\SaleDepartmentServices;

use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlansService
{
    private ReportInfo $reportInfo;

    public function prepareData(ReportInfo $reportInfo): void
    {
        $this->reportInfo = $reportInfo;
    }

    private function getPlan(int $planType): ?WorkPlan
    {
        return $this->reportInfo->workPlans->firstWhere('type', $planType);
    }

    public function monthPlan(): Collection
    {

        return  collect(
            [
                'goal' => $this->reportInfo->monthWorkPlanGoal,
                'value' => $this->reportInfo->newMoney,
                'completed' => $this->reportInfo->newMoney >= $this->reportInfo->monthWorkPlanGoal ? true : false,
            ]
        );
    }

    public function doublePlan(): Collection
    {

        $res = collect(
            [
                'goal' => $this->reportInfo->monthWorkPlanGoal * 2,
                'value' => $this->reportInfo->newMoney,
                'completed' => false,
                'bonus' => 0
            ]
        );

        $completed = $this->reportInfo->newMoney >= $this->reportInfo->monthWorkPlanGoal * 2 ? true : false;

        if ($completed) {
            $res['completed'] = true;

            $planInstance = $this->getPlan(WorkPlan::DOUBLE_PLAN);

            if ($planInstance) {
                $res['bonus'] = $planInstance->bonus;
                $this->reportInfo->bonuses += $planInstance->bonus;
            }
        }

        return $res;
    }

    public function bonusPlan(): Collection
    {
        $res = collect(
            [
                'value' => $this->reportInfo->newMoney,
                'completed' => false,
                'bonus' => 0,
            ]
        );


        $planInstance = $this->getPlan(WorkPlan::BONUS_PLAN);

        if ($planInstance) {
            $plan = $planInstance->goal;

            $res['goal'] = $plan;

            if ($this->reportInfo->newMoney >= $plan && $plan != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->bonus;
                $this->reportInfo->bonuses += $planInstance->bonus;
            }
        }


        return $res;
    }

    public function weeksPlan(): Collection
    {
        $res = collect();
        $weekPlan = $this->reportInfo->monthWorkPlanGoal / 4;
        $weeks = DateHelper::splitMonthIntoWeek($this->reportInfo->date);
        $trackedContractsIds = collect();

        foreach ($weeks as $week) {

            $newFilteredPayments = $this->reportInfo->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFiltredContractsIds = $newFilteredPayments->pluck('contract_id')->unique();

            $newUniqueContractsIds = $newFiltredContractsIds->diff($trackedContractsIds);

            if ($trackedContractsIds->isEmpty()) {
                $trackedContractsIds = $newFiltredContractsIds;
            } else {
                $trackedContractsIds = $trackedContractsIds->merge($newUniqueContractsIds);
            }

            $newUniqueContracts = $this->reportInfo->contracts->whereIn('id', $newUniqueContractsIds);

            $oldFilteredPayments = $this->reportInfo->oldPayments->filter(function ($payment) use ($week) {
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

            $serviceCounts = ServiceCountHelper::calculateServiceCountsByContracts($newUniqueContracts);
            $weekResult = collect($weekResult)->merge($serviceCounts)->toArray();
            if ($newFilteredPaymentsSum >= $weekPlan) {
                $weekResult['completed'] = true;
                $weekPlanInstance = $this->getPlan(WorkPlan::WEEK_PLAN);
                if ($weekPlanInstance != null) {
                    $weekResult['bonus'] = $weekPlanInstance->bonus;
                    $this->reportInfo->bonuses += $weekPlanInstance->bonus;
                }
            }
            $res[] = $weekResult;
        };
        return $res;
    }

    public function weeksReport(): Collection
    {
        $res = collect();
        $weeks = DateHelper::splitMonthIntoWeek($this->reportInfo->date);
        $trackedContractsIds = collect();

        foreach ($weeks as $week) {

            $newFilteredPayments = $this->reportInfo->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['date_start'], $week['date_end']);
            });

            $newFiltredContractsIds = $newFilteredPayments->pluck('contract_id')->unique();

            $newUniqueContractsIds = $newFiltredContractsIds->diff($trackedContractsIds);

            if ($trackedContractsIds->isEmpty()) {
                $trackedContractsIds = $newFiltredContractsIds;
            } else {
                $trackedContractsIds = $trackedContractsIds->merge($newUniqueContractsIds);
            }

            $newUniqueContracts = $this->reportInfo->contracts->whereIn('id', $newUniqueContractsIds);

            $oldFilteredPayments = $this->reportInfo->oldPayments->filter(function ($payment) use ($week) {
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

            $serviceCounts = ServiceCountHelper::calculateServiceCountsByContracts($newUniqueContracts);
            $weekResult = collect($weekResult)->merge($serviceCounts)->toArray();
            $res[] = $weekResult;
        };
        return $res;
    }

    public function totalValues(): Collection
    {
        $res = collect([
            'newMoney' => $this->reportInfo->newMoney,
            'oldMoney' => $this->reportInfo->oldMoney,
            ServiceCategory::INDIVIDUAL_SITE => $this->reportInfo->services[ServiceCategory::INDIVIDUAL_SITE],
            ServiceCategory::READY_SITE => $this->reportInfo->services[ServiceCategory::READY_SITE],
            ServiceCategory::RK => $this->reportInfo->services[ServiceCategory::RK],
            ServiceCategory::SEO => $this->reportInfo->services[ServiceCategory::SEO],
            ServiceCategory::OTHER => $this->reportInfo->services[ServiceCategory::OTHER],
        ]);

        return $res;
    }

    public function bServicesPlan(int $planType): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);
        // dd($this->reportInfo);
        $mainDepartmentId = $this->reportInfo->mainDepartmentId;
        $isCompletedPlan = true;
        foreach ($this->reportInfo->services as $key => $service) {
            $servicePlan = $this->reportInfo->workPlans
                ->filter(function ($workPlan) use ($planType, $mainDepartmentId, $key) {
                    return $workPlan->type === $planType
                        && $workPlan->department_id === $mainDepartmentId
                        && isset($workPlan->serviceCategory)
                        && $workPlan->serviceCategory->type === $key;
                })
                ->first();
            if ($servicePlan) {
                if ($service < $servicePlan->goal) {
                    $isCompletedPlan = false;
                    break;
                }
            }
        }

        $bPlan = $this->getPlan($planType);

        if ($bPlan) {
            $bPlan->type == WorkPlan::B1_PLAN ? $res['bonus'] = '-' . $bPlan->bonus : '';
        }

        if (!$isCompletedPlan) {
            return $res;
        }

        $res['completed'] = true;

        if ($bPlan->type != WorkPlan::B1_PLAN) {
            $res['bonus'] =  $bPlan->bonus;
        }


        return $res;
    }


    public function superPlan(Collection $weeks): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
            'value' => $this->reportInfo->newMoney,
        ]);

        $plan = $this->getPlan(WorkPlan::SUPER_PLAN);

        if (is_null($plan)) {
            return $res;
        }

        $res['goal'] = $plan->goal;

        if ($this->reportInfo->newMoney >= $plan->goal) {
            $res['completed'] = true;
            $res['bonus'] = $plan->bonus;
            $this->reportInfo->bonuses += $plan->bonus;
            return $res;
        }

        $weeksCompleted = $this->calculateWeeksCompleted($weeks, $this->reportInfo->monthWorkPlan);

        if (!$weeksCompleted->every(fn($weekStat) => $weekStat['completed'])) {
            return $res;
        }


        $res['completed'] = true;
        $res['bonus'] = $plan->bonus;
        $this->reportInfo->bonuses += $plan->bonus;

        return $res;
    }

    public function b3Plan(): Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        if ($this->reportInfo->newPayments->isEmpty()) {
            return $result;
        }

        $b3Plan = $this->getPlan(WorkPlan::B3_PLAN);
        if (!$b3Plan) {
            return $result;
        }

        $result['goal'] = $b3Plan->goal;

        $contractIds = $this->reportInfo->newPayments->pluck('contract_id');

        $contracts = $this->reportInfo->contracts->whereIn('id', $contractIds);

        if ($contracts->isEmpty()) {
            return $result;
        }

        $complexSales = 0;
        $totalContracts = $contracts->count();

        foreach ($contracts as $contract) {
            $hasSite = false;
            $hasAdditional = false;

            foreach ($contract->services as $service) {
                $category = $service->category;

                if (!$category) {
                    continue;
                }

                if (in_array($category->id, [ServiceCategory::INDIVIDUAL_SITE, ServiceCategory::READY_SITE])) {
                    $hasSite = true;
                }

                if (in_array($category->id, [ServiceCategory::RK, ServiceCategory::SEO])) {
                    $hasAdditional = true;
                }

                if ($hasSite && $hasAdditional) {
                    $complexSales++;
                    break;
                }
            }
        }

        if ($complexSales === 0) {
            return $result;
        }

        $percentage = ($complexSales / $totalContracts) * 100;

        if ($percentage > $b3Plan->goal) {
            $result['value'] = $percentage;
            $result['completed'] = true;
            $result['bonus'] = $b3Plan->bonus;
            $this->reportInfo->bonuses += $b3Plan->bonus;
        }

        return $result;
    }

    public function b4Plan(): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $b4Plan = $this->getPlan(WorkPlan::B4_PLAN);

        if ($b4Plan == null) {
            return $res;
        }

        $res['goal'] = $b4Plan->goal;

        if (! collect($this->reportInfo->services)->has(ServiceCategory::RK)) {
            return $res;
        }

        $res['value'] = $this->reportInfo->services[ServiceCategory::RK];

        if ($this->reportInfo->services[ServiceCategory::RK] >= $b4Plan->goal) {
            $res['completed'] = true;
            $res['bonus'] = $b4Plan->bonus;
            $this->reportInfo->bonuses += $b4Plan->bonus;
        };

        return $res;
    }

    private function calculateWeeksCompleted(Collection $weeks, WorkPlan $plan): Collection
    {
        $weeksCompleted = collect();

        foreach ($weeks as $key => $week) {
            $weekStat = collect([
                'completed' => false,
                'order' => $key,
            ]);

            if ($week['newMoney'] >= $plan->goal / 4) {
                $weekStat['completed'] = true;
            }

            if ($key > 0) {
                $this->checkPreviousWeek($weeksCompleted, $key, $week['newMoney'], $plan->goal);
            }

            $weeksCompleted->push($weekStat);
        }

        return $weeksCompleted;
    }

    public function calculateSalary(Collection $report): Collection
    {
        $monthWorked = $this->reportInfo->user->getMonthWorked($this->reportInfo->date);
        $bonus = null;

        $res = collect([
            'bonuses' => $this->reportInfo->bonuses,
            'newMoney' => 0,
            'oldMoney' => 0,
            'amount' => $this->reportInfo->bonuses
        ]);

        $noPercentageMonth = $this->reportInfo->workPlans->where('type', WorkPlan::NO_PERCENTAGE_MONTH)->first();


        if ($noPercentageMonth != null && $monthWorked > $noPercentageMonth->month) {
            $minimalWorkPlan = WorkPlan::where('type', WorkPlan::PERCENT_LADDER)
                ->where('department_id', $this->reportInfo->mainDepartmentId)
                ->whereNotNull('goal')
                ->orderBy('goal', 'asc')
                ->skip(1)
                ->first();
            if ($this->reportInfo->newMoney < $minimalWorkPlan->goal) {
                $bonus = 0;
            }
        }

        $workPlan = WorkPlan::where('goal', '>', $this->reportInfo->newMoney)
            ->where('type', WorkPlan::PERCENT_LADDER)
            ->where('department_id', $this->reportInfo->mainDepartmentId)
            ->orderBy('goal')
            ->first();

        if ($workPlan == null) {
            $workPlan = WorkPlan::where('type', WorkPlan::PERCENT_LADDER)
                ->where('department_id', $this->reportInfo->mainDepartmentId)
                ->orderBy('bonus', 'desc')
                ->first();
        }

        if ($workPlan == null) {
            return $res;
        }

        $bonus !== 0 ? $bonus = $workPlan->bonus : '';

        $res['percentage'] = $bonus;
        $res['newMoney'] = ($this->reportInfo->newMoney * $bonus) / 100;
        $res['oldMoney'] = ($this->reportInfo->oldMoney * $bonus) / 100;

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];


        if (!$report['b1']['completed']) {
            $b1Plan = WorkPlan::where('type', WorkPlan::B1_PLAN)
                ->where('department_id', $this->reportInfo->mainDepartmentId)
                ->first();
            $res['newMoney'] = $res['newMoney'] - ($res['newMoney'] * ($b1Plan->bonus / 100));
            $res['oldMoney'] = $res['oldMoney'] - ($res['oldMoney'] * ($b1Plan->bonus / 100));
            $res['bonuses'] = $res['bonuses'] - ($res['bonuses'] * ($b1Plan->bonus / 100));
        };

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];

        if ($report['b2']['completed']) {
            $res['amount'] = $res['amount'] * (1 + $report['b2']['bonus'] / 100);
        }

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
