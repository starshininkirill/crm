<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Models\Contract;
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

    public function attachPerformers(Contract $contract, array $data)
    {
        $allPivotData = [];

        $contractUsers = $contract->users;
        foreach ($data as $role) {
            if (empty($role['performers'])) {
                continue;
            }

            foreach ($role['performers'] as $userId) {
                $allPivotData[$userId] = ['role' => $role['id']];
            }
        }

        $contract->users()->sync($allPivotData);

        return true;
    }
}
