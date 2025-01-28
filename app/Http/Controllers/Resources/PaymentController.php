<?php

namespace App\Http\Controllers\Resources;

use App\Classes\Bitrix;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGeneratorRequest;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::query();
        if($request->get('payment')){
            $payment = Payment::where('id', $request->get('payment'))->first();
            $query->where('inn', '=', $payment->inn)
                ->whereNotNull('contract_id');
        }

        $query->where('status', Payment::STATUS_WAIT);

        $payments = $query->get();
        $payments = $payments->map(function($payment){
            return[
                'id' => $payment->id,
                'value' => $payment->value,
                'inn' => $payment->inn,
                'contract' => $payment->contract,
            ];
        });
        return $payments;
    }

    public function store(PaymentGeneratorRequest $request)
    {
        $data = $request->validated();

        Bitrix::generatePaymentDocument($data);
        
    }
}
