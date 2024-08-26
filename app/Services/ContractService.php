<?php

namespace App\Services;

use App\Models\RoleInContract;
use Illuminate\Support\Collection;
use App\Models\Contract;
use App\Models\User;

class ContractService
{
    public function addPaymentsToContract(Contract $contract, array $payments, int $maxPayments = 5)
    {
        $order = 1;

        foreach ($payments as $payment) {
            if (!empty($payment) && $order <= $maxPayments) {
                $contract->payments()->create([
                    'value' => $payment,
                    'status' => 'open',
                    'order' => $order,
                ]);
                $order++;
            }
        }
    }

    public function getPerformers(Contract $contract): Collection
    {
        $roles = RoleInContract::all();

        $performers = $contract->belongsToMany(User::class, 'contract_user')
            ->wherePivotNull('payment_id')
            ->withPivot('role_in_contracts_id')
            ->get()
            ->groupBy(function ($performer) {
                return $performer->pivot->role_in_contracts_id;
            });

        $result = $roles->map(function ($role) use ($performers) {
            return [
                'role' => $role,
                'performers' => $performers->get($role->id, collect())
            ];
        });

        return $result;
    }

    public function attachPerformer(Contract $contract, int $userId, int $roleId): bool
    {
        $exists = $contract->users()
            ->wherePivotNull('payment_id')
            ->wherePivot('user_id', $userId)
            ->wherePivot('role_in_contracts_id', $roleId)
            ->exists();

        if (!$exists) {
            $contract->users()->attach($userId, [
                'role_in_contracts_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return true;
        }
        return false;
    }
}
