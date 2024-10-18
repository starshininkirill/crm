<?php

namespace App\Services\SaleDepartmentServices;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\Contract;
use App\Models\Department;
use App\Models\ServiceCategory;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\SaleDepartmentServices\PlansService;
use Illuminate\Support\Facades\DB;

class ReportService
{

    private $newMoney = 0;
    private $oldMoney = 0;
    private $mounthWorkPlan;
    private $mounthWorkPlanGoal;
    private $workingDays;
    private $payments;
    private $newPayments;
    private $oldPayments;
    private $contracts;
    private $department;
    private $date;
    private $user;
    private $services;
    private $bonuses = 0;
    private $workPlans;


    protected $planService;

    public function __construct(PlansService $planService)
    {
        $this->planService = $planService;
    }

    private function prepareData(Carbon $date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
        $this->department = Department::getMainSaleDepartment();

        $this->workPlans = WorkPlan::where('department_id', $this->department->id)->get();

        $this->workingDays = DateHelper::getWorkingDaysInMonth($date);
        $this->mounthWorkPlan = $this->getMounthPlan();
        $this->mounthWorkPlanGoal = $this->mounthWorkPlan->goal;

        $this->payments = $this->getPaymentsForUserGroupByType();
        $this->newPayments = $this->payments->has(Payment::TYPE_NEW) ? $this->payments->get(Payment::TYPE_NEW) : collect();
        $this->oldPayments = $this->payments->has(Payment::TYPE_OLD) ? $this->payments->get(Payment::TYPE_OLD) : collect();

        $this->contracts = $this->getContractsByPayments();

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->services = $this->calculateServiceCountsByContracts($this->contracts);
    }

    public function generateUserMotivationReportData(Carbon $date, User $user): Collection
    {
        $this->prepareData($date, $user);

        $report = collect();
        $dataForReport = [
            'workPlans' => $this->workPlans,
            'contracts' => $this->contracts,
            'departmentId' => $this->department->id,
            'newPayments' => $this->newPayments,
            'oldPayments' => $this->oldPayments,
            'plan' => $this->mounthWorkPlan,
            'planGoal' => $this->mounthWorkPlanGoal,
            'date' => $this->date,
            'services' => $this->services
        ];

        $this->planService->prepareData($dataForReport);
        $report['mounthPlan'] = $this->planService->mounthPlan();
        $report['doublePlan'] = $this->planService->doublePlan();
        $report['bonusPlan'] = $this->planService->bonusPlan();
        $report['weeksPlan'] = $this->planService->weeksPlan();
        $report['superPlan'] = $this->planService->superPlan($report['weeksPlan']);
        $report['totalValues'] = $this->planService->totalValues();
        $report['b1'] = $this->planService->bServicesPlan(WorkPlan::B1_PLAN);
        $report['b2'] = $this->planService->bServicesPlan(WorkPlan::B1_PLAN);
        $report['b3'] = $this->planService->b3Plan();
        $report['b4'] = $this->planService->b4Plan();
        $report['salary'] = $this->calculateSalary($report);

        // dd($report);
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


    private function getMounthPlan(): ?WorkPlan
    {
        $monthsWorked = $this->user->getMounthWorked();
        $departmentId = $this->department->id;
        $userPositionId = $this->user->position->id;

        $mounthPlan = $this->workPlans->first(function ($plan) use ($departmentId, $userPositionId) {
            return $plan->department_id == $departmentId &&
                $plan->position_id == $userPositionId &&
                $plan->type == WorkPlan::MOUNTH_PLAN;
        });

        if ($mounthPlan) {
            return $mounthPlan;
        }

        $mounthPlan = $this->workPlans->first(function ($plan) use ($departmentId, $monthsWorked) {
            return $plan->department_id == $departmentId &&
                $plan->mounth == $monthsWorked &&
                $plan->type == WorkPlan::MOUNTH_PLAN;
        });

        if ($mounthPlan) {
            return $mounthPlan;
        }

        return $this->workPlans->where('type', WorkPlan::MOUNTH_PLAN)
            ->sortByDesc('mounth')
            ->first();
    }

    private function getContractsByPayments(): Collection
    {
        $uniqueIds = $this->newPayments->pluck('contract_id')->unique();
        return Contract::whereIn('id', $uniqueIds)
            ->with(['services.category'])
            ->get();
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

        foreach ($contracts as $contract) {
            foreach ($contract->services as $service) {
                if ($service->category) {
                    $this->incrementServiceCount($counts, $service->category->type);
                }
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
