<?php

namespace App\Services\AdvertisingReports\Builders;

use App\Models\Contracts\Contract;
use App\Models\Contracts\ServiceMonth;
use App\Models\Finance\Payment;
use App\Models\States\Contract\Introduction;
use App\Models\UserManagement\Department;
use App\Services\AdvertisingReports\DTO\ReportDataDTO;
use Carbon\Carbon;

class ReportDataDTOBuilder
{
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

            $isNewTarif = false;

            $next = $months
                ->first(function ($item) use ($serviceMonth) {
                    return $item->start_service_date > $serviceMonth->start_service_date;
                });

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

        $users = $users->map(function ($user) use ($previousMonthlyServices, $nextServiceMonths, $pairs) {
            $user->current_months = $previousMonthlyServices->where('user_id', $user->id);
            $user->next_months = $nextServiceMonths->where('user_id', $user->id);

            $user->new_tarif_count = $pairs->filter(function ($pair) use ($user) {
                return $pair['is_new_tarif'] && 
                       $pair['prev']->user_id == $user->id;
            })->count();

            return $user;
        });

        return new ReportDataDTO(
            $date,
            $activeContracts,
            $allServiceMonths,
            $previousMonthlyServices,
            $nextServiceMonths,
            $pairs,
            $users
        );
    }
}
