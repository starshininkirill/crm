<?php

namespace App\Services\ProjectReports\Builders;

use App\Helpers\DateHelper;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Department;
use App\Models\Payment;
use App\Models\User;
use App\Models\WorkPlan;
use App\Services\ProjectReports\DTO\ReportDataDTO;
use App\Services\ProjectReports\DTO\UserDataDTO;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportDataDTOBuilder
{
    public function __construct(
        private UserService $userService,
        private WorkPlanService $workPlanService,
    ) {}

    public function buildFullReport(Carbon $date, Department $department): ReportDataDTO
    {
        $data = $this->prepareFullData($date, $department);
        return $data;
    }

    public function prepareFullData(Carbon $date, ?Department $department)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        
        $plans = $this->workPlanService->actualPlans($date, $department);

        $users = $department->allUsers($date, ['departmentHead']);
        $activeUsers = $this->userService->filterUsersByStatus($users, 'active', $endOfMonth);
        $userIds = $activeUsers->pluck('id');

        $upsails = $this->getUpsails($endOfMonth, $userIds, ContractUser::SELLER);
        $closeContracts = $this->getCloseContracts($endOfMonth, $userIds);
        $accountSeceivable = $this->getAccountSeceivable($endOfMonth, $userIds, ContractUser::PROJECT);

        return new ReportDataDTO(
            $upsails,
            $department,
            $activeUsers,
            $closeContracts,
            $accountSeceivable,
            $plans,
        );
    }

    public function getUserSubdata(ReportDataDTO $mainData, User $user): UserDataDTO
    {
        $upsails = $mainData->upsails->filter(function ($upsail) use ($user) {
            return $upsail->contract->contractUsers->where('role', ContractUser::SELLER)->contains('user_id', $user->id);
        });
        $upsailsMoney = $upsails->sum('value');

        $closeContracts = $mainData->closeContracts->filter(function ($contract) use ($user) {
            return $contract->contractUsers->where('role', ContractUser::PROJECT)->contains('user_id', $user->id);
        });
        
        $accountSeceivable = $mainData->accountSeceivable->filter(function ($payment) use ($user) {
            $contractUsers = $payment->contract->contractUsers;
            $isProjectManager = $contractUsers
                ->where('role', ContractUser::PROJECT)
                ->where('user_id', $user->id)
                ->isNotEmpty();
            $isSeller = $contractUsers
                ->where('role', ContractUser::SELLER)
                ->where('user_id', $user->id)
                ->isNotEmpty();
            return $isProjectManager && !$isSeller;
        });

        $individualSites = $this->calculateIndividualSites($closeContracts, $mainData->workPlans);
        $readySites = $this->calculateReadySites($closeContracts, $mainData->workPlans);
        $compexes = $this->calculateCompexes($closeContracts);

        return new UserDataDTO(
            $upsails,
            $upsailsMoney,
            $mainData->department,
            $user,
            $closeContracts,
            $accountSeceivable,
            $mainData->workPlans,
            $individualSites,
            $readySites,
            $compexes,
        );
    }

    protected function getCloseContracts(Carbon $date, array|Collection $userIds)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        if (DateHelper::isCurrentMonth($date)) {
            $contractsQuery = Contract::query()
                ->whereBetween('close_date', [$startOfMonth, $endOfMonth]);

            $contractsQuery->whereHas('contractUsers', function ($query) use ($userIds) {
                $query->where('role', ContractUser::PROJECT)
                    ->whereIn('user_id', $userIds);
            });

            return $contractsQuery->with(['contractUsers.user', 'payments', 'services.category'])->get();
        } else {
            $targetContractIds = ContractUser::getLatestHistoricalRecordsQuery($endOfMonth)
                ->where('new_values->role', ContractUser::PROJECT)
                ->whereIn('new_values->user_id', $userIds)
                ->get()
                ->pluck('new_values.contract_id')
                ->unique();

            if ($targetContractIds->isEmpty()) {
                return collect();
            }

            $contracts = Contract::whereIn('id', $targetContractIds)
                ->whereBetween('close_date', [$startOfMonth, $endOfMonth])
                ->with(['services.category'])
                ->get();

            if ($contracts->isEmpty()) {
                return collect();
            }

            $contractIds = $contracts->pluck('id');

            $historicalContractUsers = ContractUser::recreateFromQuery(
                ContractUser::getLatestHistoricalRecordsQuery($endOfMonth)
                    ->whereIn('new_values->contract_id', $contractIds),
                ['user'],
                $endOfMonth
            )->groupBy('contract_id');

            $historicalPayments = Payment::recreateFromQuery(
                Payment::getLatestHistoricalRecordsQuery($endOfMonth)
                    ->whereIn('new_values->contract_id', $contractIds),
                [],
                $endOfMonth
            )->groupBy('contract_id');

            $contracts->each(function ($contract) use ($historicalContractUsers, $historicalPayments) {
                $contract->setRelation('contractUsers', $historicalContractUsers->get($contract->id, collect()));
                $contract->setRelation('payments', $historicalPayments->get($contract->id, collect()));
            });

            return $contracts;
        }
    }

    protected function getAccountSeceivable(Carbon $date, array|Collection $userIds, int $role)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $relations = [
            'contract.contractUsers.user'
        ];

        if (DateHelper::isCurrentMonth($date)) {
            return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->with($relations)
                ->where('status', Payment::STATUS_CLOSE)
                ->whereNot('order', 1)
                ->whereHas('contract.contractUsers', function ($query) use ($userIds, $role) {
                    $query->where('role', $role)
                        ->whereIn('user_id', $userIds);
                })
                ->get();
        } else {
            $contractUserHistories = ContractUser::getLatestHistoricalRecordsQuery($endOfMonth)
                ->whereIn('new_values->user_id', $userIds)
                ->where('new_values->role', $role)
                ->pluck('new_values')
                ->map(function ($data) {
                    return $data['contract_id'];
                });


            $paymentsHistoryQuery = Payment::getLatestHistoricalRecordsQuery($endOfMonth)
                ->whereBetween('new_values->created_at', [$startOfMonth, $endOfMonth])
                ->where('new_values->status', Payment::STATUS_CLOSE)
                ->whereNot('new_values->order', 1)
                ->whereIn('new_values->contract_id', $contractUserHistories);

            return Payment::recreateFromQuery($paymentsHistoryQuery, $relations, $endOfMonth);
        }
    }


    protected function getUpsails(Carbon $date, array|Collection $userIds, int $role)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $relations = [
            'contract.contractUsers.user',
            'contract.services'
        ];

        if (DateHelper::isCurrentMonth($date)) {
            return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->with($relations)
                ->where('status', Payment::STATUS_CLOSE)
                ->whereHas('contract.contractUsers', function ($query) use ($userIds, $role) {
                    $query->where('role', $role)
                        ->whereIn('user_id', $userIds);
                })
                ->get();
        } else {
            $contractUserHistories = ContractUser::getLatestHistoricalRecordsQuery($endOfMonth)
                ->whereIn('new_values->user_id', $userIds)
                ->where('new_values->role', $role)
                ->pluck('new_values')
                ->map(function ($data) {
                    return $data['contract_id'];
                });


            $paymentsHistoryQuery = Payment::getLatestHistoricalRecordsQuery($endOfMonth)
                ->whereBetween('new_values->created_at', [$startOfMonth, $endOfMonth])
                ->where('new_values->status', Payment::STATUS_CLOSE)
                ->whereIn('new_values->contract_id', $contractUserHistories);

            return Payment::recreateFromQuery($paymentsHistoryQuery, $relations, $endOfMonth);
        }
    }

    private function calculateIndividualSites(Collection $closeContracts, Collection $workPlans): int
    {
        return $this->calculateContractsByWorkPlanType($closeContracts, $workPlans, WorkPlan::INDIVID_CATEGORY_IDS);
    }

    private function calculateReadySites(Collection $closeContracts, Collection $workPlans): int
    {
        return $this->calculateContractsByWorkPlanType($closeContracts, $workPlans, WorkPlan::READY_SYTES_CATEGORY_IDS);
    }

    private function calculateContractsByWorkPlanType(Collection $closeContracts, Collection $workPlans, string $workPlanType): int
    {
        $categoryIds = $workPlans
            ->where('type', $workPlanType)
            ->pluck('data.categoryIds')
            ->flatten()
            ->filter();

        if ($categoryIds->isEmpty()) {
            return 0;
        }

        $contracts = $closeContracts->filter(function ($contract) use ($categoryIds) {
            return $contract->services->pluck('category.id')->intersect($categoryIds)->isNotEmpty();
        });

        return $contracts->count();
    }

    private function calculateCompexes(Collection $closeContracts): int
    {
        $contracts = $closeContracts->where('is_complex', true);

        return $contracts->count();
    }
}
