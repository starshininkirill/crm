<?php

namespace App\Services\AdvertisingReports\Builders;

use App\Helpers\DateHelper;
use App\Models\Contracts\Contract;
use App\Models\Contracts\ContractUser;
use App\Models\Contracts\ServiceMonth;
use App\Models\Finance\Payment;
use App\Models\States\Contract\Introduction;
use App\Models\UserManagement\Department;
use App\Models\UserManagement\User;
use App\Services\AdvertisingReports\DTO\ReportDataDTO;
use App\Services\AdvertisingReports\DTO\UserDataDTO;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\SaleReports\WorkPlans\WorkPlanService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportDataDTOBuilder
{

    public function __construct(
        private WorkPlanService $workPlanService,
    ) {}

    public function prepareFullData(Carbon $date, Department $department): ReportDataDTO
    {

        $users = $department->allUsers($date);
        $usersIds = $users->pluck('id');

        $activeContracts = Contract::query()
            ->with(['serviceMonths' => function ($query) use ($date, $usersIds) {
                $query->whereHas('payment', function ($q) {
                    $q->where('status', Payment::STATUS_CLOSE);
                })
                    ->where('start_service_date', '<', $date->startOfMonth())
                    ->whereIn('user_id', $usersIds)
                    ->orderByDesc('month')
                    ->limit(1);
            }, 'serviceMonths.payment', 'serviceMonths.user', 'serviceMonths.contract', 'serviceMonths.tarif'])
            ->where('type', Contract::TYPE_ADS)
            ->whereState('state', Introduction::class)
            ->get();

        $previousMonthlyServices = $activeContracts->pluck('serviceMonths')->flatten(1);


        $allServiceMonths = ServiceMonth::whereIn('contract_id', $activeContracts->pluck('id'))
            ->whereHas('payment', function ($q) {
                $q->where('status', Payment::STATUS_CLOSE);
            })
            ->with(['payment', 'user', 'contract', 'tarif'])
            ->orderBy('contract_id')
            ->orderBy('start_service_date')
            ->get();

        $grouped = $allServiceMonths->groupBy('contract_id');

        $pairs = $previousMonthlyServices->map(function ($serviceMonth) use ($grouped) {
            $months = $grouped[$serviceMonth->contract_id] ?? collect();

            $next = $months
                ->first(function ($item) use ($serviceMonth) {
                    return $item->month == $serviceMonth->month + 1;
                });

            $isNewTarif = false;

            if ($next) {
                $isNewTarif = $next->tarif->order > $serviceMonth->tarif->order;
            }

            return [
                'prev' => $serviceMonth,
                'next' => $next,
                'is_new_tarif' => $isNewTarif,
            ];
        })->values();

        $nextServiceMonths = $pairs->pluck('next')->filter();

        $plans = $this->workPlanService->actualPlans($date, $department);

        $activeUserIds = $users->pluck('id');
        $upsails = $this->getUpsails($date->endOfMonth(), $activeUserIds, ContractUser::SELLER);

        return new ReportDataDTO(
            $date,
            $activeContracts,
            $allServiceMonths,
            $previousMonthlyServices,
            $nextServiceMonths,
            $pairs,
            $users,
            $plans,
            $upsails
        );
    }

    public function getUserSubdata(ReportDataDTO $reportDataDTO, User $user): UserDataDTO
    {
        $newTarifCount = $reportDataDTO->pairs->filter(function ($pair) use ($user) {
            return $pair['is_new_tarif'] &&
                $pair['prev']->user_id == $user->id;
        })->count();

        $upsails = $reportDataDTO->upsails->filter(function ($upsail) use ($user) {
            return $upsail->contract->contractUsers->where('role', ContractUser::SELLER)->contains('user_id', $user->id);
        });

        return new UserDataDTO(
            $user,
            $reportDataDTO->date,
            collect(),
            $reportDataDTO->previousMonthlyServices->where('user_id', $user->id),
            $reportDataDTO->nextMonthlyServices->where('user_id', $user->id),
            $newTarifCount,
            collect(),
            $reportDataDTO->plans,
            $upsails,
        );
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
}
