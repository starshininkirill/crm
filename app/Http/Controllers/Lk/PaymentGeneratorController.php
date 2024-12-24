<?php

namespace App\Http\Controllers\Lk;

use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGeneratorRequest;
use App\Models\Organization;

class PaymentGeneratorController extends Controller
{

    public function create()
    {


        $organisations = Organization::all()->toArray();

        return view('lk.payment.create', [
            'organisations' => $organisations
        ]);
    }

    public function store(PaymentGeneratorRequest $request)
    {
        $data = $request->validated();

        dd($request->contractData());

        // $responseData = DocumentGenerator::generatePaymentDocument($data);

        $responseData = [];
        return back()->with(
            $responseData
        );
        
    }
}
