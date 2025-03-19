<?php

namespace App\Services\SaleDepartmentServices;

use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\CallHistory;
use App\Models\ContractUser;
use App\Models\Department;
use App\Models\Payment;
use App\Models\User;
use App\Models\WorkingDay;
use App\Models\WorkPlan;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportInfo
{
    public float|int $newMoney = 0;
    public float|int $oldMoney = 0;
    public WorkPlan $monthWorkPlan;
    public float|int $monthWorkPlanGoal;
    public Collection $workingDays;
    public Collection $payments;
    public Collection $newPayments;
    public Collection $oldPayments;
    public Collection $contracts;
    public int $mainDepartmentId;
    public Department $department;
    public Carbon $date;
    public User $user;
    public Collection $servicesByCatsCount;
    public float|int $bonuses = 0;
    public Collection $workPlans;
    public $isUserData = false;
    public $callsStat;

    private $userService;
    
    public function __construct(Carbon $date = null, User $user = null, Department $department = null)
    {
        $this->userService = new UserService();   
        if ($user == null && $date == null) {
            return null;
        }
        if ($user == null) {
            $this->prepareFullData($date, $department);
        } else {
            $this->prepareUserData($date, $user);
        }
    }

    private function prepareFullData(Carbon $date, Department $department = null): void
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();
        $this->callsStat = CallHistory::query()->whereBetween('date', [$startDate, $endDate])->get()->groupBy('phone');

        $this->date = $date;
        $mainDepartment = Department::getMainSaleDepartment();
        $this->mainDepartmentId = $mainDepartment->id;

        if ($department != null) {
            $this->department = $department;
        } else {
            $this->department = $mainDepartment;
        }

        $this->workPlans = WorkPlan::where('department_id', $this->mainDepartmentId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with('serviceCategory')
            ->get();

        if ($this->workPlans->isEmpty()) {
            throw new Exception('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $this->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);
        $this->payments = User::monthlyClosePaymentsForRoleGroup($date, $this->department->allUsers()->pluck('id'), ContractUser::SALLER);

        $this->newPayments = $this->payments->where('type', Payment::TYPE_NEW);
        $this->oldPayments = $this->payments->where('type', Payment::TYPE_OLD);

        $this->contracts = Payment::getContractsByPaymentsWithRelations($this->newPayments);

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($this->contracts);
    }

    public function getUserSubdata(User $user): ?ReportInfo
    {
        if ($this->isUserData) {
            return null;
        }

        $subdataInstance = new ReportInfo();

        $subdataInstance->callsStat = $this->callsStat->get($user->phone) ?? collect();
        $subdataInstance->date = $this->date;
        $subdataInstance->user = $user;
        $subdataInstance->mainDepartmentId = $this->mainDepartmentId;

        $subdataInstance->workPlans = $this->workPlans;

        $subdataInstance->workingDays = $this->workingDays;
        $subdataInstance->monthWorkPlan = $this->getMonthPlan($user);
        $subdataInstance->monthWorkPlanGoal = $subdataInstance->monthWorkPlan->data['goal'];

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

        $subdataInstance->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($subdataInstance->contracts);
        $subdataInstance->isUserData = true;

        return $subdataInstance;
    }


    // TODO 
    // Функция не импользуется( может не работать, нужно тестить )
    private function prepareUserData(Carbon $date, User $user)
    {
        $this->date = $date;
        $this->user = $user;
        $mainDepartment = Department::getMainSaleDepartment();
        $this->mainDepartmentId = $mainDepartment->id;

        $this->workPlans = WorkPlan::where('department_id', $this->mainDepartmentId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with('serviceCategory')
            ->get();

        if ($this->workPlans->isEmpty()) {
            throw new Exception('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $this->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $this->monthWorkPlan = $this->getMonthPlan();
        $this->monthWorkPlanGoal = $this->monthWorkPlan->data['goal'];

        $this->payments = User::monthlyClosePaymentsForRoleGroup($date, [$user->id], ContractUser::SALLER);

        $this->newPayments = $this->payments->where('type', Payment::TYPE_NEW);
        $this->oldPayments = $this->payments->where('type', Payment::TYPE_OLD);

        $this->contracts = Payment::getContractsByPaymentsWithRelations($this->newPayments);

        $this->newMoney = $this->newPayments->sum('value');
        $this->oldMoney = $this->oldPayments->sum('value');

        $this->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($this->contracts);
        $this->isUserData = true;
    }


    private function getMonthPlan(?User $user = null): ?WorkPlan
    {
        if ($user == null) {
            $user = $this->user;
        }
        $monthsWorked = $this->userService->getMonthWorked($user, $this->date);
        $departmentId = $this->mainDepartmentId;
        $userPosition = $user->position;
        $userPosition == null ? $userPositionId = null : $userPositionId = $userPosition->id;

        if ($userPositionId != null) {
            $monthPlan = $this->workPlans->first(function ($plan) use ($departmentId, $userPositionId) {
                return $plan->department_id == $departmentId &&
                    $plan->position_id == $userPositionId &&
                    $plan->type == WorkPlan::MOUNTH_PLAN;
            });
        }

        if (isset($monthPlan) && $monthPlan != null) {
            return $monthPlan;
        }

        $monthPlan = $this->workPlans->first(function ($plan) use ($departmentId, $monthsWorked) {
            return $plan->department_id == $departmentId &&
                $plan->month == $monthsWorked &&
                $plan->type == WorkPlan::MOUNTH_PLAN;
        });


        if ($monthPlan) {
            return $monthPlan;
        }

        return $this->workPlans->where('type', WorkPlan::MOUNTH_PLAN)
            ->sortByDesc('month')
            ->first();
    }
}
