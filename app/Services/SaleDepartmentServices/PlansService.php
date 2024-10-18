<?php

namespace App\Services\SaleDepartmentServices;

use App\Helpers\DateHelper;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PlansService
{

    private $departmentId;
    private $workPlans;
    private $contracts;
    private $newPayments;
    private $oldPayments;
    private $newMoney = 0;
    private $oldMoney = 0;
    private $plan;
    private $planGoal;
    private $date; 
    private $services;

    public function prepareData(array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        $this->workPlans = $workPlans;
        $this->contracts = $contracts;
        $this->departmentId = $departmentId;
        $this->newPayments = $newPayments;
        $this->oldPayments = $oldPayments;
        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');
        $this->planGoal = $planGoal;
        $this->date = $date;
        $this->services = $services;
    }

    private function getPlan(int $planType): ?WorkPlan
    {
        return $this->workPlans->firstWhere('type', $planType);
    }

    public function mounthPlan(): Collection
    {

        return  collect(
            [
                'goal' => $this->planGoal,
                'value' => $this->newMoney,
                'completed' => $this->newMoney >= $this->planGoal ? true : false,
            ]
        );
    }

    public function doublePlan(): Collection
    {

        $res = collect(
            [
                'goal' => $this->planGoal * 2,
                'value' => $this->newMoney,
                'completed' => false,
                'bonus' => 0
            ]
        );

        $completed = $this->newMoney >= $this->planGoal * 2 ? true : false;

        if ($completed) {
            $res['completed'] = true;

            $planInstance = $this->getPlan(WorkPlan::DOUBLE_PLAN);

            if ($planInstance) {
                $res['bonus'] = $planInstance->bonus;
                // $bonuses += $planInstance->bonus;
            }
        }

        return $res;
    }

    public function bonusPlan(): Collection
    {
        $res = collect(
            [
                'value' => $this->newMoney,
                'completed' => false,
                'bonus' => 0,
            ]
        );


        $planInstance = $this->getPlan(WorkPlan::BONUS_PLAN);

        if ($planInstance) {
            $plan = $planInstance->goal;

            $res['goal'] = $plan;

            if ($this->newMoney >= $plan && $plan != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->bonus;
                // $this->bonuses += $planInstance->bonus;
            }
        }


        return $res;
    }

    public function weeksPlan(): Collection
    {
        $res = collect();

        $weekPlan = $this->planGoal / 4;

        $weeks = DateHelper::splitMounthIntoWeek($this->date);

        $trackedContractsIds = collect();

        foreach ($weeks as $week) {

            $newFilteredPayments = $this->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['start'], $week['end']);
            });

            $newFiltredContractsIds = $newFilteredPayments->pluck('contract_id')->unique();

            $newUniqueContractsIds = $newFiltredContractsIds->diff($trackedContractsIds);

            if ($trackedContractsIds->isEmpty()) {
                $trackedContractsIds = $newFiltredContractsIds;
            } else {
                $trackedContractsIds = $trackedContractsIds->merge($newUniqueContractsIds);
            }

            $newUniqueContracts = $this->contracts->whereIn('id', $newUniqueContractsIds);

            $oldFilteredPayments = $this->oldPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['start'], $week['end']);
            });

            $newFilteredPaymentsSum = $newFilteredPayments->sum('value');
            $oldFilteredPaymentsSum = $oldFilteredPayments->sum('value');


            $weekResult = [
                'start' => $week['start']->format('d'),
                'end' => $week['end']->format('d'),
                'goal' => $weekPlan,
                'newMoney' => $newFilteredPaymentsSum,
                'oldMoney' => $oldFilteredPaymentsSum,
                'completed' => false,
                'bonus' => 0
            ];

            $serviceCounts = $this->calculateServiceCountsByContracts($newUniqueContracts);
            $weekResult = array_merge($weekResult, $serviceCounts);
            if ($newFilteredPaymentsSum >= $weekPlan) {
                $weekResult['completed'] = true;
                $weekPlanInstance = $this->getPlan(WorkPlan::WEEK_PLAN);
                if ($weekPlanInstance != null) {
                    $weekResult['bonus'] = $weekPlanInstance->bonus;
                    // $this->bonuses += $weekPlanInstance->bonus;
                }
            }
            $res[] = $weekResult;
        };
        return $res;
    }

    public function totalValues(): Collection
    {
        $res = collect([
            'newMoney' => $this->newMoney,
            'oldMoney' => $this->oldMoney,
            ServiceCategory::INDIVIDUAL_SITE => $this->services[ServiceCategory::INDIVIDUAL_SITE],
            ServiceCategory::READY_SITE => $this->services[ServiceCategory::READY_SITE],
            ServiceCategory::RK => $this->services[ServiceCategory::RK],
            ServiceCategory::SEO => $this->services[ServiceCategory::SEO],
            ServiceCategory::OTHER => $this->services[ServiceCategory::OTHER],
        ]);

        return $res;
    }

    public function bServicesPlan(int $planType): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);
        $departmentId = $this->departmentId;
        $isCompletedPlan = true;
        foreach ($this->services as $key => $service) {
            $servicePlan = $this->workPlans
                ->filter(function ($workPlan) use ($planType, $departmentId, $key) {
                    return $workPlan->type === $planType
                        && $workPlan->department_id === $departmentId
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

        $b1Plan = $this->getPlan($planType);

        if ($b1Plan) {
            $b1Plan->type == WorkPlan::B1_PLAN ? $res['bonus'] = '-' . $b1Plan->bonus : '';
        }

        if (!$isCompletedPlan) {
            return $res;
        }

        $res['completed'] = true;

        if ($b1Plan) {
            $b1Plan->type == WorkPlan::B1_PLAN ? $res['bonus'] = 0 : $b1Plan->bonus;
        }

        return $res;
    }


    public function superPlan(Collection $weeks): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
            'value' => $this->newMoney,
        ]);

        $plan = $this->getPlan(WorkPlan::SUPER_PLAN);

        if (is_null($plan)) {
            return $res;
        }

        $res['goal'] = $plan->goal;

        if ($this->newMoney >= $plan->goal) {
            $res['completed'] = true;
            $res['bonus'] = $plan->bonus;
            return $res;
        }

        $weeksCompleted = $this->calculateWeeksCompleted($weeks, $this->plan);

        if (!$weeksCompleted->every(fn($weekStat) => $weekStat['completed'])) {
            return $res;
        }


        $res['completed'] = true;
        $res['bonus'] = $plan->bonus;
        // $this->bonuses += $plan->bonus;

        return $res;
    }

    public function b3Plan(): Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        if ($this->newPayments->isEmpty()) {
            return $result;
        }
        // Получаем план B3
        $b3Plan = $this->getPlan(WorkPlan::B3_PLAN);
        if (!$b3Plan) {
            return $result;
        }

        $result['goal'] = $b3Plan->goal;

        $contractIds = $this->newPayments->pluck('contract_id');
        
        $contracts = $this->contracts->whereIn('id', $contractIds);
        
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
            // $this->bonuses += $b3Plan->bonus;
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

        if (! array_key_exists(ServiceCategory::RK, $this->services)) {
            return $res;
        }

        $res['value'] = $this->services[ServiceCategory::RK];

        if ($this->services[ServiceCategory::RK] >= $b4Plan->goal) {
            $res['completed'] = true;
            $res['bonus'] = $b4Plan->bonus;
            // $this->bonuses += $b4Plan->bonus;
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

    private function checkPreviousWeek(Collection &$weeksCompleted, int $currentKey, float $currentWeekMoney, float $planGoal): void
    {
        $previousWeek = $weeksCompleted[$currentKey - 1];
        if (!$previousWeek['completed'] && $currentWeekMoney >= $planGoal / 2) {
            $weeksCompleted[$currentKey - 1]['completed'] = true;
        }
    }



    private function calculateServiceCountsByContracts(Collection $contracts): array
    {
        $counts = [
            ServiceCategory::INDIVIDUAL_SITE => 0,
            ServiceCategory::READY_SITE => 0,
            ServiceCategory::RK => 0,
            ServiceCategory::SEO => 0,
            ServiceCategory::OTHER => 0,
        ];

        foreach ($contracts as $contract) {
            foreach ($contract->services as $service) {
                if ($service->category) {
                    $this->incrementServiceCount($counts, $service->category->type);
                }
            }
        }

        return $counts;
    }


    private function incrementServiceCount(array &$counts, string $serviceType): void
    {
        switch ($serviceType) {
            case ServiceCategory::INDIVIDUAL_SITE:
                $counts[ServiceCategory::INDIVIDUAL_SITE]++;
                break;
            case ServiceCategory::READY_SITE:
                $counts[ServiceCategory::READY_SITE]++;
                break;
            case ServiceCategory::RK:
                $counts[ServiceCategory::RK]++;
                break;
            case ServiceCategory::SEO:
                $counts[ServiceCategory::SEO]++;
                break;
            case ServiceCategory::OTHER:
                $counts[ServiceCategory::OTHER]++;
                break;
        }
    }
}
