<?php

namespace App\Services;

use App\Models\Payment;
use Exception;

class PaymentService
{
    public function store(array $data): bool
    {
        $payment = Payment::create($data);
        try{

        }catch(Exception $expeption){
            return false;
        }
        return true;
    }
}
