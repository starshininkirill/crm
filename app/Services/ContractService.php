<?php

namespace App\Services;

use App\Models\Contract;

class ContractService
{
    public function addPaymentsToContract(Contract $contract, array $payments, int $maxPayments = 5)
    {
        $order = 1;

        foreach ($payments as $payment) {
            if (!empty($payment) && $order <= $maxPayments) {
                $contract->payments()->create([
                    'value' => $payment,
                    'status' => 'close',
                    'order' => $order,
                ]);
                $order++;
            }
        }
    }
}
