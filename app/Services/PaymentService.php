<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\WorkPlan;
use Illuminate\Support\Collection;
use PhpParser\Node\Expr\Cast\Bool_;

class PaymentService
{

    public function update(Payment $payment, array|Collection $data): bool
    {
        return $payment->update($data);
    }
}
