<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentStoreRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(
        PaymentService $paymentService,
    ) {
        $this->paymentService = $paymentService;
    }

    public function create()
    {
        $paymentMethods = PaymentMethod::all();
        return view('lk.payment.create', ['paymentMethods' => $paymentMethods]);
    }

    public function store(PaymentStoreRequest $request)
    {
        $data = $request->validated();
        $data['status'] = Payment::STATUS_CONFIRMATION; 
        $this->paymentService->store($data);
        return redirect()->back()->with('success', 'Платёж успешно создан!');
    }
}
