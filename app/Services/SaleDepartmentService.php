<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\Contract;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SaleDepartmentService
{

    private $newMoney = 0;
    private $oldMoney = 0;
    private $mounthWorkPlan;
    private $mounthWorkPlanGoal;
    private $workingDays;
    private $payments;
    private $newPayments;
    private $oldPayments;
    private $uniqueNewPayments;
    private $department;
    private $date;
    private $user;
    private $services;
    private $bonuses = 0;

    private function prepareData(Carbon $date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
        $this->department = Department::getMainSaleDepartment();

        $this->workingDays = DateHelper::getWorkingDaysInMonth($date);
        $this->mounthWorkPlan = $this->getMounthPlan();
        $this->mounthWorkPlanGoal = $this->mounthWorkPlan->goal;

        $this->payments = $this->getPaymentsForUserGroupByType();
        $this->newPayments = $this->payments->has(Payment::TYPE_NEW) ? $this->payments->get(Payment::TYPE_NEW) : collect();
        $this->oldPayments = $this->payments->has(Payment::TYPE_OLD) ? $this->payments->get(Payment::TYPE_OLD) : collect();
        $this->uniqueNewPayments = $this->newPayments->unique('contract_id');

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->services = $this->calculateServiceCountsByPayments($this->newPayments);
    }

    public function generateUserMotivationReportData(Carbon $date, User $user): Collection
    {
        $this->prepareData($date, $user);

        $report = collect();

        $report['mounthPlan'] = $this->generateMounthPlanReport();
        $report['doublePlan'] = $this->generateDoubleMounthPlanReport();
        $report['bonusPlan'] = $this->generateBonusMounthPlanReport();
        $report['weeksPlan'] = $this->generateWeeksMounthPlanReport();
        $report['superPlan'] = $this->generateSuperPlanReport($report['weeksPlan']);
        $report['totalValues'] = $this->generateTotalMounthValuesReport();
        $report['b1'] = $this->generateBServiceMounthPlanReport(WorkPlan::B1_PLAN);
        $report['b2'] = $this->generateBServiceMounthPlanReport(WorkPlan::B2_PLAN);
        $report['b3'] = $this->generateB3MounthPlanReport();
        $report['b4'] = $this->generateB4MounthPlanReport();
        $report['salary'] = $this->calculateSalary($report);

        return $report;
    }

    public function generateUserReportData(Carbon $date, User $user): Collection
    {
        $this->prepareData($date, $user);

        $report = collect();

        if ($this->newPayments) {
            $newPaymentsGroupedByDate = $this->groupPaymentsByDate($this->newPayments ?? collect());
            $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate($this->newPayments->unique('contract_id') ?? collect());
        } else {
            $newPaymentsGroupedByDate = collect();
            $uniqueNewPaymentsGroupedByDate = collect();
        }

        if ($this->oldPayments) {
            $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($this->oldPayments ?? collect());
        } else {
            $oldPaymentsGroupedByDate = collect();
        }

        foreach ($this->workingDays as $day) {
            $dayFormatted = Carbon::parse($day)->format('Y-m-d');
            $report[] = $this->generateDailyReport($dayFormatted, $newPaymentsGroupedByDate, $oldPaymentsGroupedByDate, $uniqueNewPaymentsGroupedByDate);
        }

        return $report;
    }

    private function calculateSalary(Collection $report): Collection
    {
        $mounthWorked = $this->user->getMounthWorked();
        $bonus = null;


        $res = collect([
            'bonuses' => $this->bonuses,
            'newMoney' => 0,
            'oldMoney' => 0,
            'amount' => $this->bonuses
        ]);

        // TODO
        // Добавить в настройках значение после которого до 60к не будет процента
        if ($mounthWorked > 3) {
            $minimalWorkPlan = WorkPlan::where('type', WorkPlan::PERCENT_LADDER)
                ->where('department_id', $this->department->id)
                ->orderBy('goal', 'asc')
                ->skip(1)
                ->first();
            if ($this->newMoney < $minimalWorkPlan->goal) {
                $bonus = 0;
            }
        }

        $workPlan = WorkPlan::where('goal', '<', $this->newMoney)
            ->where('type', WorkPlan::PERCENT_LADDER)
            ->where('department_id', $this->department->id)
            ->orderBy('goal', 'desc')
            ->first();

        if ($workPlan == null) {
            $workPlan = WorkPlan::where('type', WorkPlan::PERCENT_LADDER)
                ->where('department_id', $this->department->id)
                ->orderBy('goal', 'desc')
                ->first();
        }

        if ($workPlan == null) {
            return $res;
        }

        $bonus !== 0 ? $bonus = $workPlan->bonus : '';


        $res['percentage'] = $bonus;
        $res['newMoney'] = ($this->newMoney * $bonus) / 100;
        $res['oldMoney'] = ($this->oldMoney * $bonus) / 100;

        $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];


        if (!$report['b1']['completed']) {
            $b1Plan = WorkPlan::where('type', WorkPlan::B1_PLAN)
                ->where('department_id', $this->department->id)
                ->first();
            $res['newMoney'] = $res['newMoney'] - ($res['newMoney'] * ($b1Plan->bonus / 100));
            $res['oldMoney'] = $res['oldMoney'] - ($res['oldMoney'] * ($b1Plan->bonus / 100));
            $res['bonuses'] = $res['bonuses'] - ($res['bonuses'] * ($b1Plan->bonus / 100));

            $res['amount'] = $res['newMoney'] + $res['oldMoney'] + $res['bonuses'];
        };

        if (!$report['b1']['completed']) {
            $res['amount'] = $res['amount'] * (1 - $report['b1']['bonus'] / 100);
        }


        if ($report['b2']['completed']) {
            $res['amount'] = $res['amount'] * (1 + $report['b2']['bonus'] / 100);
        }

        return $res;
    }

    private function generateSuperPlanReport(Collection $weeks): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
            'value' => $this->newMoney,
        ]);

        $plan = WorkPlan::where('type', WorkPlan::SUPER_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

        if (is_null($plan)) {
            return $res;
        }

        $res['goal'] = $plan->goal;

        $weeksCompleted = $this->calculateWeeksCompleted($weeks, $this->mounthWorkPlan);

        if (!$weeksCompleted->every(fn($weekStat) => $weekStat['completed'])) {
            return $res;
        }
        

        if ($this->newMoney < $plan->goal) {
            return $res;
        }


        $res['completed'] = true;
        $res['bonus'] = $plan->bonus;
        $this->bonuses += $plan->bonus;

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

    private function generateB4MounthPlanReport(): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $b4Plan = WorkPlan::where('type', WorkPlan::B4_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

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
            $this->bonuses += $b4Plan->bonus;
        };

        return $res;
    }

    private function generateB3MounthPlanReport(): Collection
    {
        $result = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        if ($this->newPayments->isEmpty()) {
            return $result;
        }

        $b3Plan = WorkPlan::where('type', WorkPlan::B3_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

        if (!$b3Plan) {
            return $result;
        }

        $result['goal'] = $b3Plan->goal;

        $contracts = $this->newPayments
            ->map(function ($payment) {
                return $payment->contract()->with('services.category')->first();
            })
            ->filter();

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
            $this->bonuses += $b3Plan->bonus;
        }

        return $result;
    }
    private function generateBServiceMounthPlanReport(int $plan): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $isCompletedPlan = true;
        foreach ($this->services as $key => $service) {
            $servicePlan = WorkPlan::where('work_plans.type', $plan)
                ->where('work_plans.department_id', $this->department->id)
                ->join('service_categories', 'work_plans.service_category_id', '=', 'service_categories.id')
                ->where('service_categories.type', $key)
                ->first();
            if ($servicePlan) {
                if ($service < $servicePlan->goal) {
                    $isCompletedPlan = false;
                    break;
                }
            }
        }

        $b1Plan = WorkPlan::where('type', $plan)
            ->where('department_id', $this->department->id)
            ->first();

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

    private function generateTotalMounthValuesReport(): Collection
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

    private function generateWeeksMounthPlanReport(): Collection
    {

        $res = collect();

        $weekPlan = $this->mounthWorkPlanGoal / 4;

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

            $newUniqueContracts = Contract::whereIn('id', $newUniqueContractsIds)->get();

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
                $weekPlanInstance = WorkPlan::where('type', WorkPlan::WEEK_PLAN)->first();
                if ($weekPlanInstance != null) {
                    $weekResult['bonus'] = $weekPlanInstance->bonus;
                    $this->bonuses += $weekPlanInstance->bonus;
                }
            }
            $res[] = $weekResult;
        };
        return $res;
    }

    private function generateMounthPlanReport(): Collection
    {

        return  collect(
            [
                'goal' => $this->mounthWorkPlanGoal,
                'value' => $this->newMoney,
                'completed' => $this->newMoney >= $this->mounthWorkPlanGoal ? true : false,
            ]
        );
    }

    private function generateBonusMounthPlanReport(): Collection
    {
        $res = collect(
            [
                'value' => $this->newMoney,
                'completed' => false,
                'bonus' => 0,
            ]
        );

        $planInstance = WorkPlan::where('type', WorkPlan::BONUS_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

        if ($planInstance) {
            $plan = $planInstance->goal;

            $res['goal'] = $plan;

            if ($this->newMoney >= $plan && $plan != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->bonus;
                $this->bonuses += $planInstance->bonus;
            }
        }


        return $res;
    }
    private function generateDoubleMounthPlanReport(): Collection
    {

        $res = collect(
            [
                'goal' => $this->mounthWorkPlanGoal * 2,
                'value' => $this->newMoney,
                'completed' => false,
                'bonus' => 0
            ]
        );

        $completed = $this->newMoney >= $this->mounthWorkPlanGoal * 2 ? true : false;

        if ($completed) {
            $res['completed'] = true;

            $planInstance = WorkPlan::where('type', WorkPlan::DOUBLE_PLAN)
                ->where('department_id', $this->department->id)
                ->first();

            if ($planInstance) {
                $res['bonus'] = $planInstance->bonus;
                $this->bonuses += $planInstance->bonus;
            }
        }

        return $res;
    }

    private function getMounthPlan()
    {

        $monthsWorked = $this->user->getMounthWorked();


        $departmentId = $this->department->id;
        $userPositionId = $this->user->position->id;

        $mounthPlan = WorkPlan::where('department_id', $departmentId)
            ->where('position_id', $userPositionId)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->first();

        if ($mounthPlan) {
            return $mounthPlan;
        }

        $mounthPlan = WorkPlan::where('department_id', $departmentId)
            ->where('mounth', $monthsWorked)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->first();

        if ($mounthPlan) {
            return $mounthPlan;
        }

        return WorkPlan::where('department_id', $departmentId)
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->orderBy('mounth', 'desc')
            ->first();
    }


    private function getPaymentsForUserGroupByType(): Collection
    {
        $startOfMonth = $this->date->copy()->startOfMonth();
        $endOfMonth = $this->date->copy()->endOfMonth();
        $contractIds = $this->user->contracts->pluck('id')->unique();


        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('contract_id', $contractIds)
            ->where('status', Payment::STATUS_CLOSE)
            ->get()
            ->groupBy('type');
    }

    private function groupPaymentsByDate(Collection $payments): Collection
    {
        $workingDays = $this->workingDays;
        return $payments->groupBy(function ($payment) use ($workingDays) {
            $paymentDate = Carbon::parse($payment->created_at);

            return in_array($paymentDate, $this->workingDays)
                ? $paymentDate
                : DateHelper::getNearestPreviousWorkingDay($paymentDate);
        });
    }

    private function generateDailyReport(string $day, Collection $newPaymentsGroupedByDate, Collection $oldPaymentsGroupedByDate, Collection $uniqueNewPaymentsGroupedByDate): array
    {
        $dayNewPayments = $newPaymentsGroupedByDate->get($day, collect());
        $dayOldPayments = $oldPaymentsGroupedByDate->get($day, collect());
        $uniqueDayNewPayments = $uniqueNewPaymentsGroupedByDate->get($day, collect());

        $newPaymentsSum = $dayNewPayments->sum('value');
        $oldPaymentsSum = $dayOldPayments->sum('value');
        $serviceCounts = $this->calculateServiceCountsByPayments($uniqueDayNewPayments);

        return [
            'date' => $day,
            'newMoney' => $newPaymentsSum,
            'oldMoney' => $oldPaymentsSum,
            'individualSites' => $serviceCounts[ServiceCategory::INDIVIDUAL_SITE],
            'readiesSites' => $serviceCounts[ServiceCategory::READY_SITE],
            'rk' => $serviceCounts[ServiceCategory::RK],
            'seo' => $serviceCounts[ServiceCategory::SEO],
            'other' => $serviceCounts[ServiceCategory::OTHER],
        ];
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

        foreach ($contracts->unique('id') as $contract) {
            foreach ($contract->services as $service) {
                $this->incrementServiceCount($counts, $service->category->type);
            }
        }

        return $counts;
    }

    private function calculateServiceCountsByPayments(Collection $payments): array
    {
        $counts = [
            ServiceCategory::INDIVIDUAL_SITE => 0,
            ServiceCategory::READY_SITE => 0,
            ServiceCategory::RK => 0,
            ServiceCategory::SEO => 0,
            ServiceCategory::OTHER => 0,
        ];

        foreach ($payments->unique('contract_id') as $payment) {
            foreach ($payment->contract->services as $service) {
                $this->incrementServiceCount($counts, $service->category->type);
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
