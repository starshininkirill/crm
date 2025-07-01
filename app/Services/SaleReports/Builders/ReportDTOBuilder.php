<?php

namespace App\Services\SaleReports\Builders;

use App\Exceptions\Business\BusinessException;
use App\Helpers\DateHelper;
use App\Helpers\ServiceCountHelper;
use App\Models\CallHistory;
use App\Models\ContractUser;
use App\Models\Department;
use App\Models\Payment;
use App\Models\User;
use App\Models\WorkingDay;
use App\Models\WorkPlan;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportDTOBuilder
{
    private UserService $userService;
    private WorkPlanService $workPlanService;
    private $userRelations = [];

    public function __construct(
        UserService $userService,
        WorkPlanService $workPlanService
    ) {
        $this->userService = $userService;
        $this->workPlanService = $workPlanService;
    }

    public function setUserRelations(array $relations = [])
    {
        $this->userRelations = $relations;
    }

    public function buildHeadReport(Carbon $date, ?Department $department = null): ReportDTO
    {
        $data = new ReportDTO();
        $this->prepareHeadData($data, $date, $department);
        return $data;
    }

    public function buildFullReport(Carbon $date, ?Department $department = null): ReportDTO
    {
        $data = new ReportDTO();
        $this->prepareFullData($data, $date, $department);
        return $data;
    }

    public function buildSingleUserReport(Carbon $date, User $user): ReportDTO
    {
        $data = new ReportDTO();
        $this->prepareUserData($data, $date, $user);
        return $data;
    }

    private function prepareHeadData(ReportDTO $data, Carbon $date, ?Department $department, ?Collection $users = null): void
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $data->date = $endDate;

        $data->mainDepartmentId = Department::getMainSaleDepartment()?->id;
        $data->department = $department;

        $data->workPlans = $this->workPlanService->actualSalePlans($date);

        $activeUsers = $users ?? $this->userService->filterUsersByStatus($data->department->allUsers($data->date, $this->userRelations), 'active', $data->date);

        $activeUsers = $activeUsers->where('id', '!=', $department->head?->id);

        $data->payments = $this->monthlyClosePaymentsForRoleGroup(
            $date,
            $activeUsers->pluck('id'),
            ContractUser::SELLER
        );

        $data->newPayments = $data->payments->where('type', Payment::TYPE_NEW);
        $data->oldPayments = $data->payments->where('type', Payment::TYPE_OLD);

        $data->contracts = $data->newPayments->pluck('contract')->filter()->unique('id');

        $data->newMoney = $data->newPayments->sum('value');
        $data->oldMoney = $data->oldPayments->sum('value');
    }

    public function buildHeadSubReport(ReportDTO $mainData, User $user): ?ReportDTO
    {

        if ($mainData->isUserData) {
            return null;
        }

        $subData = new ReportDTO();

        $subData->date = $mainData->date->copy()->endOfMonth();
        $subData->mainDepartmentId = $mainData->mainDepartmentId;
        $subData->department = $mainData->department;
        $subData->workPlans = $mainData->workPlans;
        $subData->workingDays = $mainData->workingDays;

        $subData->user = $user;

        $subData->monthWorkPlan = $this->getMonthPlan($mainData->workPlans, $user, $subData->date, $mainData->mainDepartmentId);
        $subData->monthWorkPlanGoal = $subData->monthWorkPlan->data['goal'];

        $subData->payments = $this->filterPaymentsByUser($mainData, $user);

        $subData->newPayments = $subData->payments->where('type', Payment::TYPE_NEW);
        $subData->oldPayments = $subData->payments->where('type', Payment::TYPE_OLD);

        $subData->newMoney = $subData->newPayments->sum('value');
        $subData->oldMoney = $subData->oldPayments->sum('value');

        $subData->isUserData = true;

        return $subData;
    }

    private function prepareFullData(ReportDTO $data, Carbon $date, ?Department $department): void
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $data->date = $endDate;
        $data->callsStat = CallHistory::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('phone');

        $mainDepartment = Department::getMainSaleDepartment();
        $data->mainDepartmentId = $mainDepartment->id;
        $data->department = $department ?? $mainDepartment;

        $data->workPlans = $this->workPlanService->actualSalePlans($date, ['serviceCategory']);


        if ($data->workPlans->isEmpty()) {
            throw new BusinessException('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $data->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $activeUsers = $this->userService->filterUsersByStatus($data->department->allUsers($data->date), 'active', $data->date);

        $data->payments = $this->monthlyClosePaymentsForRoleGroup(
            $date,
            $activeUsers->pluck('id'),
            ContractUser::SELLER
        );

        $data->newPayments = $data->payments->where('type', Payment::TYPE_NEW);
        $data->oldPayments = $data->payments->where('type', Payment::TYPE_OLD);

        $data->contracts = $data->newPayments->pluck('contract')->filter()->unique('id');

        $data->newMoney = $data->newPayments->sum('value');
        $data->oldMoney = $data->oldPayments->sum('value');

        $data->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($data->contracts);
        $data->financeWeeks = DateHelper::splitMonthIntoWeek($date);
    }

    public function getUserSubdata(ReportDTO $mainData, User $user): ?ReportDTO
    {
        if ($mainData->isUserData) {
            return null;
        }

        $subData = new ReportDTO();

        // Копируем общие данные
        $subData->date = $mainData->date->copy()->endOfMonth();
        $subData->mainDepartmentId = $mainData->mainDepartmentId;
        $subData->workPlans = $mainData->workPlans;
        $subData->workingDays = $mainData->workingDays;


        $subData->user = $user;
        $subData->callsStat = $mainData->callsStat->get($user->phone) ?? collect();
        $subData->monthWorkPlan = $this->getMonthPlan($mainData->workPlans, $user, $subData->date, $mainData->mainDepartmentId);
        $subData->monthWorkPlanGoal = $subData->monthWorkPlan->data['goal'];

        $subData->payments = $this->filterPaymentsByUser($mainData, $user);

        $mainData->usedPayments = $mainData->usedPayments->merge(
            $subData->payments->diff($mainData->usedPayments)
        );

        $subData->newPayments = $subData->payments->where('type', Payment::TYPE_NEW);
        $subData->oldPayments = $subData->payments->where('type', Payment::TYPE_OLD);

        $subData->contracts = $mainData->contracts->filter(
            fn($contract) => $contract->users->contains('id', $user->id)
        );

        $subData->newMoney = $subData->newPayments->sum('value');
        $subData->oldMoney = $subData->oldPayments->sum('value');

        $subData->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($subData->contracts);
        $subData->financeWeeks = $mainData->financeWeeks;
        $subData->isUserData = true;

        return $subData;
    }


    private function prepareUserData(ReportDTO $data, Carbon $date, User $user): void
    {
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $data->date = $endDate;
        $data->user = $user;
        $data->isUserData = true;

        $mainDepartment = Department::getMainSaleDepartment();
        if (!$mainDepartment) {
            return;
        }

        $data->mainDepartmentId = $mainDepartment->id;
        $data->department = $user->department;

        $data->workPlans = $this->workPlanService->actualSalePlans($date, ['serviceCategory']);

        if ($data->workPlans->isEmpty()) {
            throw new BusinessException('Нет планов для рассчёта');
        }

        if ($user->phone) {
            $data->callsStat = CallHistory::query()
                ->where('phone', $user->phone)
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->groupBy('phone');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $data->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $data->monthWorkPlan = $this->getMonthPlan($data->workPlans, $user, $data->date, $data->mainDepartmentId);
        if ($data->monthWorkPlan) {
            $data->monthWorkPlanGoal = $data->monthWorkPlan->data['goal'];
        }

        $data->payments = $this->monthlyClosePaymentsForRoleGroup($date, [$user->id], ContractUser::SELLER);

        $data->newPayments = $data->payments->where('type', Payment::TYPE_NEW);
        $data->oldPayments = $data->payments->where('type', Payment::TYPE_OLD);

        $data->contracts = $data->newPayments->pluck('contract')->filter()->unique('id');

        $data->newMoney = $data->newPayments->sum('value');
        $data->oldMoney = $data->oldPayments->sum('value');

        $data->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($data->contracts);
        $data->financeWeeks = DateHelper::splitMonthIntoWeek($date);
    }

    private function filterPaymentsByUser(ReportDTO $mainData, User $user)
    {

        $test = $mainData->payments->filter(
            function ($payment) use ($user, $mainData) {
                if (!$payment->contract) {
                    return false;
                }

                $contractUsers = $payment->contract->users;

                $userInContract = $contractUsers->contains('id', $user->id);

                if (!$userInContract) {
                    return false;
                }

                if ($user->fired_at) {
                    return $payment->created_at <= $user->fired_at;
                }

                return true;
            }
        );

        return $test;
    }

    private function getMonthPlan(Collection $workPlans, ?User $user = null, $date, $mainDepartmentId): ?WorkPlan
    {
        $monthsWorked = $this->userService->getMonthWorked($user, $date);

        $userPosition = $user->position;
        $userPositionId = $userPosition?->id;

        // Поиск по позиции
        if ($userPositionId) {
            $monthPlan = $workPlans->first(
                fn($plan) => $plan->department_id == $mainDepartmentId &&
                    $plan->position_id == $userPositionId &&
                    $plan->type == WorkPlan::MOUNTH_PLAN
            );
            if ($monthPlan) return $monthPlan;
        }


        // Поиск по количеству отработанных месяцев
        $monthPlan = $workPlans->first(
            function ($plan) use ($mainDepartmentId, $monthsWorked) {
                if ($plan->position_id != null || $plan->type != WorkPlan::MOUNTH_PLAN) {
                    return false;
                }
                return $plan->department_id == $mainDepartmentId &&
                    $plan->data['month'] == $monthsWorked;
            }
        );
        if ($monthPlan) return $monthPlan;


        // Возвращаем последний доступный план
        return $workPlans
            ->where('type', WorkPlan::MOUNTH_PLAN)
            ->sortByDesc('month')
            ->first();
    }

    private function monthlyClosePaymentsForRoleGroup(Carbon $date, array|Collection $userIds, int $role)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $relations = [
            'contract.services.category',
            'contract.users.position'
        ];

        if (DateHelper::isCurrentMonth($date)) {
            return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', Payment::STATUS_CLOSE)
                ->whereHas('contract.contractUsers', function ($query) use ($userIds, $role) {
                    $query->where('role', $role)
                        ->whereIn('user_id', $userIds);
                })
                ->with($relations)
                ->get();
        } else {
            $historicalContractIds = ContractUser::getLatestHistoricalRecordsQuery($date)
                ->whereIn('new_values->user_id', $userIds)
                ->where('new_values->role', $role)
                ->pluck('new_values')
                ->map(function ($data) {
                    return $data['contract_id'];
                });


            $paymentsHistoryQuery = Payment::getLatestHistoricalRecordsQuery($date)
                ->whereBetween('new_values->created_at', [$startOfMonth, $endOfMonth])
                ->where('new_values->status', Payment::STATUS_CLOSE)
                ->whereIn('new_values->contract_id', $historicalContractIds);

            return Payment::recreateFromQuery($paymentsHistoryQuery, $relations);
        }
    }
}
