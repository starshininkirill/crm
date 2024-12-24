<?php

namespace App\Http\Controllers\Resources;

use App\Classes\Bitrix;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGeneratorRequest;

class PaymentController extends Controller
{

    public function store(PaymentGeneratorRequest $request)
    {
        $data = $request->validated();

        Bitrix::generatePaymentDocument($data);
        

    }
}
