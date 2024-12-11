<?php

namespace App\Http\Controllers\Resources;

use App\Classes\Bitrix;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{

    public function store(PaymentRequest $request)
    {
        $data = $request->validated();

        Bitrix::generatePaymentDocument($data);
        

    }
}
