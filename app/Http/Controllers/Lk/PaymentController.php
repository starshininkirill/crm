<?php

namespace App\Http\Controllers\Lk;

use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Organization;
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
        $organisations = Organization::all()->toArray();

        return view('lk.payment.create', [
            'organisations' => $organisations
        ]);
    }

    public function store(PaymentRequest $request)
    {
        $data = $request->validated();

        $responseData = DocumentGenerator::generatePaymentDocument($data);

        return back()->with(
            $responseData
        );
        
    }
}
