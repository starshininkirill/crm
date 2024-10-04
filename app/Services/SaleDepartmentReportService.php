<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SaleDepartmentReportService
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


        $this->services = $this->calculateServiceCounts($this->uniqueNewPayments);
    }

    public function generateUserMotivationReportData(Carbon $date, User $user): array
    {
        $this->prepareData($date, $user);

        $report = [];

        $report['mounth_plan'] = $this->generateMounthPlanReport();
        $report['double_plan'] = $this->generateDoubleMounthPlanReport();
        $report['bonus_plan'] = $this->generateBonusMounthPlanReport();
        $report['weeks_plan'] = $this->generateWeeksMounthPlanReport();
        $report['total_values'] = $this->generateTotalMounthValuesReport();
        $report['b1'] = $this->generateBServiceMounthPlanReport(WorkPlan::B1_PLAN);
        $report['b2'] = $this->generateBServiceMounthPlanReport(WorkPlan::B2_PLAN);
        $report['b4'] = $this->generateB4MounthPlanReport();


        // dd($report);
        return $report;
    }

    private function generateB4MounthPlanReport(): Collection
    {
        $res = collect([
            'completed' => false,
            'bonus' => 0,
        ]);

        $b3Plan = WorkPlan::where('type', WorkPlan::B3_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

        if ($b3Plan == null) {
            return $res;
        }
        if ($this->services[ServiceCategory::RK] >= $b3Plan->goal) {
            $res['completed'] = true;
            $res['bonus'] = $b3Plan->bonus;
        };

        return $res;
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
                ->join('service_categories', 'work_plans.service_category_id', '=', 'service_categories.id') // соединяем таблицы
                ->where('service_categories.type', $key)
                ->first();
            if ($servicePlan) {
                if ($service < $servicePlan->goal) {
                    $isCompletedPlan = false;
                    break;
                }
            }
        }

        if (!$isCompletedPlan) {
            return $res;
        }

        $res['completed'] = true;
        $b1Plan = WorkPlan::where('type', $plan)
            ->where('department_id', $this->department->id)
            ->first();
        if ($b1Plan) {
            $res['bonus'] = $b1Plan->bonus;
        }

        return $res;
    }

    private function generateTotalMounthValuesReport(): Collection
    {
        $res = collect([
            'new_money' => $this->newMoney,
            'old_money' => $this->oldMoney,
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

        foreach ($weeks as $week) {
            $newFilteredPayments = $this->newPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['start'], $week['end']);
            });
            $oldFilteredPayments = $this->oldPayments->filter(function ($payment) use ($week) {
                return $payment->created_at->between($week['start'], $week['end']);
            });

            $newFilteredPaymentsSum = $newFilteredPayments->sum('value');
            $oldFilteredPaymentsSum = $oldFilteredPayments->sum('value');


            $weekResult = [
                'start' => $week['start']->format('Y-m-d'),
                'end' => $week['end']->format('Y-m-d'),
                'goal' => $weekPlan,
                'new_money' => $newFilteredPaymentsSum,
                'old_money' => $oldFilteredPaymentsSum,
                'completed' => false,
                'bonus' => 0
            ];

            $serviceCounts = $this->calculateServiceCounts($newFilteredPayments);

            $weekResult = array_merge($weekResult, $serviceCounts);

            if ($newFilteredPaymentsSum >= $weekPlan) {
                $weekResult['completed'] = true;
                $weekPlanInstance = WorkPlan::where('type', WorkPlan::WEEK_PLAN)->first();
                if ($weekPlanInstance != null) {
                    $weekResult['bonus'] = $weekPlanInstance->bonus;
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
                'plan' => $this->mounthWorkPlanGoal,
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
                'bonus' => 0
            ]
        );

        $planInstance = WorkPlan::where('type', WorkPlan::BONUS_PLAN)
            ->where('department_id', $this->department->id)
            ->first();

        if ($planInstance) {
            $plan = $planInstance->goal;

            $res['plan'] = $plan;

            if ($this->newMoney >= $plan && $plan != null) {
                $res['completed'] = true;
                $res['bonus'] = $planInstance->bonus;
            }
        }


        return $res;
    }
    private function generateDoubleMounthPlanReport(): Collection
    {

        $res = collect(
            [
                'plan' => $this->mounthWorkPlanGoal * 2,
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
            }
        }

        return $res;
    }

    private function getMounthPlan()
    {
        $employmentDate = $this->user->getFirstWorkingDay();
        $nowDate = Carbon::now();

        $startWorkingDay = $employmentDate->format('d');

        $monthsWorked = $employmentDate->floorMonth()->diffInMonths($nowDate->floorMonth()) + 1;
        if ($startWorkingDay > 7) {
            $monthsWorked--;
        }
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
        $serviceCounts = $this->calculateServiceCounts($uniqueDayNewPayments);

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

    private function calculateServiceCounts(Collection $payments): array
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
