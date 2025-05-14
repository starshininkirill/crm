<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Http\Resources\ContractForShortlistResource;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ContractService
{
    public function storeSubContract(Collection|array $data)
    {
        $parentContract = Contract::where('number', $data['number'])->first();

        if (!$parentContract) {
            throw new BusinessException('Не существует родительского договора');
        }

        $service = Service::findOrFail($data['service_id']);

        return DB::transaction(function () use ($parentContract, $data, $service) {
            $contract = Contract::create([
                'parent_id' => $parentContract->id,
                'number' => $service->name,
                'amount_price' => $data['amount_price'],
                'organization_id' => $data['organization_id'] ?? null,
            ]);

            $contract->services()->attach($data['service_id'], [
                'price' => $data['amount_price'],
            ]);

            return $contract;
        });
    }

    public function searchContract(string $s)
    {
        $contract = Contract::where('number', $s)->first();

        if (!$contract) {
            throw new BusinessException('Договор не найден');
        }

        return new ContractForShortlistResource($contract);
    }

    public function attachPerformers(Contract $contract, array $data)
    {
        $contractUsers = $this->groupUsersByRole($contract);

        foreach ($data as $role) {
            $newPerformers = collect($role['performers'])->unique();

            if ($newPerformers->isEmpty()) {
                $this->detachAllForRole($contract, $contractUsers, $role['id']);
                continue;
            }

            if ($contractUsers->has($role['id'])) {
                $this->detachMissingUsers($contract, $contractUsers, $newPerformers, $role['id']);
            }

            $this->attachNewUsers($contract, $contractUsers, $role['id'], $role['performers']);
        }

        return true;
    }

    private function groupUsersByRole(Contract $contract)
    {
        return $contract->allUsers()->groupBy(function ($user) {
            return $user->pivot->role;
        });
    }

    private function detachAllForRole(Contract $contract, $contractUsers, $roleId)
    {
        if ($contractUsers->has($roleId)) {
            $contractUsers[$roleId]->each(function ($user) use ($contract, $roleId) {
                $contract->users()->newPivotStatementForId($user->id)->where('role', $roleId)->delete();
            });
        }
    }

    private function detachMissingUsers(Contract $contract, $contractUsers, $newPerformers, $roleId)
    {
        $currentUsers = $contractUsers[$roleId];
        $usersToDetach = $currentUsers->reject(function ($user) use ($newPerformers) {
            return $newPerformers->contains($user->id);
        })->pluck('id');

        if ($usersToDetach->isNotEmpty()) {
            $usersToDetach->each(function ($userId) use ($contract, $roleId) {
                // $contract->users()->newPivotStatementForId($userId)->where('role', $roleId)->delete();
                $record = ContractUser::where('contract_id', $contract->id)
                    ->where('user_id', $userId)
                    ->where('role', $roleId)
                    ->first();

                if ($record) {
                    $record->delete();
                }
            });
        }
    }

    private function attachNewUsers(Contract $contract, $contractUsers, $roleId, $performers)
    {
        foreach ($performers as $userId) {
            if (!$this->isUserAttachedToRole($contractUsers, $roleId, $userId)) {
                // $contract->users()->attach($userId, ['role' => $roleId]);

                ContractUser::create([
                    'contract_id' => $contract->id,
                    'user_id' => $userId,
                    'role' => $roleId
                ]);
            }
        }
    }

    private function isUserAttachedToRole($contractUsers, $roleId, $userId)
    {
        return $contractUsers->has($roleId) && $contractUsers[$roleId]->pluck('id')->contains($userId);
    }
}
