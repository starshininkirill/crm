<?php

namespace App\Services\SaleDepartmentServices;

use App\DTO\SaleDepartment\ReportDTO;
use App\Factories\SaleDepartment\ReportFactory;
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

class ReportBuilder
{
    private UserService $userService;
    private WorkPlanService $workPlanService;

    public function __construct(
        UserService $userService,
        WorkPlanService $workPlanService
    ) {
        $this->userService = $userService;
        $this->workPlanService = $workPlanService;
    }

    public static function factory(): ReportFactory
    {
        return new ReportFactory();
    }

    public function buildFullReport(Carbon $date, ?Department $department = null): ReportDTO
    {
        $data = new ReportDTO();
        $this->prepareFullData($data, $date, $department);
        return $data;
    }

    public function buildUserReport(Carbon $date, User $user): ReportDTO
    {
        $data = new ReportDTO();
        $this->prepareUserData($data, $date, $user);
        return $data;
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
            throw new Exception('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $data->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $activeUsers = $this->userService->filterUsersByStatus($data->department->allUsers($data->date), 'active', $data->date);

        $data->payments = $this->monthlyClosePaymentsForRoleGroup(
            $date,
            $activeUsers->pluck('id'),
            ContractUser::SALLER
        );

        $data->newPayments = $data->payments->where('type', Payment::TYPE_NEW);
        $data->oldPayments = $data->payments->where('type', Payment::TYPE_OLD);

        $data->contracts = Payment::getContractsByPaymentsWithRelations($data->newPayments);

        $data->newMoney = $data->newPayments->sum('value');
        $data->oldMoney = $data->oldPayments->sum('value');

        $data->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($data->contracts);
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

        // Устанавливаем пользовательские данные
        $subData->user = $user;
        $subData->callsStat = $mainData->callsStat->get($user->phone) ?? collect();
        $subData->monthWorkPlan = $this->getMonthPlan($mainData->workPlans, $user, $mainData->date, $mainData->mainDepartmentId);
        $subData->monthWorkPlanGoal = $subData->monthWorkPlan->data['goal'];


        // Фильтрация данных по пользователю
        $subData->payments = $mainData->payments->filter(
            fn($payment) => $payment->contract && $payment->contract->allUsers($subData->date)->contains('id', $user->id)
        );

        $mainData->usedPayments = $mainData->usedPayments->merge(
            $subData->payments->diff($mainData->usedPayments)
        );

        $subData->newPayments = $subData->payments->where('type', Payment::TYPE_NEW);
        $subData->oldPayments = $subData->payments->where('type', Payment::TYPE_OLD);

        $subData->contracts = $mainData->contracts->filter(
            fn($contract) => $contract->allUsers($subData->date)->contains('id', $user->id)
        );

        $subData->newMoney = $subData->newPayments->sum('value');
        $subData->oldMoney = $subData->oldPayments->sum('value');

        $subData->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($subData->contracts);
        $subData->isUserData = true;

        return $subData;
    }


    private function prepareUserData(ReportDTO $data, Carbon $date, User $user): void
    {
        $data->date = $date;
        $data->user = $user;
        $mainDepartment = Department::getMainSaleDepartment();
        $data->mainDepartmentId = $mainDepartment->id;

        $data->workPlans = WorkPlan::where('department_id', $data->mainDepartmentId)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->with('serviceCategory')
            ->get();

        if ($data->workPlans->isEmpty()) {
            throw new Exception('Нет планов для рассчёта');
        }

        $workingDays = WorkingDay::whereYear('date', $date->format('Y'))->get();
        $data->workingDays = DateHelper::getWorkingDaysInMonth($date, $workingDays);

        $data->monthWorkPlan = $this->getMonthPlan($data->workPlans, $user, $data->date, $data->mainDepartmentId);
        $data->monthWorkPlanGoal = $data->monthWorkPlan->data['goal'];

        $data->payments = User::monthlyClosePaymentsForRoleGroup($date, [$user->id], ContractUser::SALLER);

        $data->newPayments = $data->payments->where('type', Payment::TYPE_NEW);
        $data->oldPayments = $data->payments->where('type', Payment::TYPE_OLD);

        $data->contracts = Payment::getContractsByPaymentsWithRelations($data->newPayments);

        $data->newMoney = $data->newPayments->sum('value');
        $data->oldMoney = $data->oldPayments->sum('value');

        $data->servicesByCatsCount = ServiceCountHelper::calculateServiceCountsByContracts($data->contracts);
        $data->isUserData = true;
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


        if (DateHelper::isCurrentMonth($date)) {
            return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', Payment::STATUS_CLOSE)
                ->whereHas('contract.contractUsers', function ($query) use ($userIds, $role) {
                    $query->whereIn('user_id', $userIds)
                        ->where('role', $role);
                })
                ->with([
                    'contract.services.category',
                    'contract.users'
                ])
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

            return Payment::recreateFromQuery($paymentsHistoryQuery, ['contract.services.category', 'contract.users']);
        }
    }
}
