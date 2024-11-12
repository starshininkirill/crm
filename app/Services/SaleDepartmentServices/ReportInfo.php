<?php

namespace App\Services\SaleDepartmentServices;

use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\Department;
use App\Models\Payment;
use App\Models\User;
use App\Models\WorkingDay;
use App\Models\WorkPlan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportInfo
{
    public float|int $newMoney = 0;
    public float|int $oldMoney = 0;
    public WorkPlan $mounthWorkPlan;
    public float|int $mounthWorkPlanGoal;
    public Collection $workingDays;
    public Collection $payments;
    public Collection $newPayments;
    public Collection $oldPayments;
    public Collection $contracts;
    public int $departmentId;
    public Carbon $date;
    public User $user;
    public Collection $services;
    public float|int $bonuses = 0;
    public Collection $workPlans;
    public $isUserData = false;

    public function __construct(Carbon $date = null, User $user = null)
    {
        if ($user == null && $date == null) {
            return null;
        }
        if ($user == null) {
            $this->prepareFullData($date);
        } else {
            $this->prepareUserData($date, $user);
        }
    }

    private function prepareFullData(Carbon $date): void
    {
        $this->date = $date;
        $this->departmentId = Department::getMainSaleDepartment()->id;

        $this->workPlans = WorkPlan::where('department_id', $this->departmentId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with('serviceCategory')
            ->get();

        if ($this->workPlans->isEmpty()) {
            throw new Exception('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $this->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);
        $this->payments = Payment::getMonthlyPayments($date);

        $this->newPayments = $this->payments->where('type', Payment::TYPE_NEW);
        $this->oldPayments = $this->payments->where('type', Payment::TYPE_OLD);

        $this->contracts = Payment::getContractsByPaymentsWithRelations($this->newPayments);

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->services = ServiceCountHelper::calculateServiceCountsByContracts($this->contracts);
    }

    public function getUserSubdata(User $user): ?ReportInfo
    {
        if ($this->isUserData) {
            return null;
        }

        $subdataInstance = new ReportInfo();

        $subdataInstance->date = $this->date;
        $subdataInstance->user = $user;
        $subdataInstance->departmentId = $this->departmentId;

        $subdataInstance->workPlans = $this->workPlans;

        $subdataInstance->workingDays = $this->workingDays;
        $subdataInstance->mounthWorkPlan = $this->getMounthPlan($user);
        $subdataInstance->mounthWorkPlanGoal = $subdataInstance->mounthWorkPlan->goal;


        $subdataInstance->payments = $this->payments->filter(function ($payment) use ($user) {
            return $payment->contract && $payment->contract->users->contains('id', $user->id);
        });

        $subdataInstance->newPayments = $subdataInstance->payments->where('type', Payment::TYPE_NEW);
        $subdataInstance->oldPayments = $subdataInstance->payments->where('type', Payment::TYPE_OLD);

        $subdataInstance->contracts = $this->contracts->filter(function ($contract) use ($user) {
            return $contract->users->contains('id', $user->id);
        });

        $subdataInstance->newMoney = $subdataInstance->newPayments->sum('value');
        $subdataInstance->oldMoney = $subdataInstance->oldPayments->sum('value');

        $subdataInstance->services = ServiceCountHelper::calculateServiceCountsByContracts($subdataInstance->contracts);
        $subdataInstance->isUserData = true;

        return $subdataInstance;
    }


    private function prepareUserData(Carbon $date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
        $this->departmentId = Department::getMainSaleDepartment()->id;

        $this->workPlans = WorkPlan::where('department_id', $this->departmentId)->get();

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $this->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $this->mounthWorkPlan = $this->getMounthPlan();
        $this->mounthWorkPlanGoal = $this->mounthWorkPlan->goal;

        $this->payments = $this->user->monthlyClosePaymentsWithRelations($this->date);

        $this->newPayments = $this->payments->where('type', Payment::TYPE_NEW);
        $this->oldPayments = $this->payments->where('type', Payment::TYPE_OLD);

        $this->contracts = Payment::getContractsByPaymentsWithRelations($this->newPayments);

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->services = ServiceCountHelper::calculateServiceCountsByContracts($this->contracts);
        $this->isUserData = true;
    }


    private function getMounthPlan(?User $user = null): ?WorkPlan
    {

        if ($user == null) {
            $user = $this->user;
        }
        $monthsWorked = $user->getMounthWorked($this->date);
        $departmentId = $this->departmentId;
        $userPosition = $user->position;
        $userPosition == null ? $userPositionId = null : $userPositionId = $userPosition->id;

        if ($userPositionId != null) {
            $mounthPlan = $this->workPlans->first(function ($plan) use ($departmentId, $userPositionId) {
                return $plan->department_id == $departmentId &&
                    $plan->position_id == $userPositionId &&
                    $plan->type == WorkPlan::MOUNTH_PLAN;
            });
        }

        if (isset($mounthPlan) && $mounthPlan != null) {
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
}
