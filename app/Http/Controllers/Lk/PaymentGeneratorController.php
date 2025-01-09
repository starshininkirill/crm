<?php

namespace App\Http\Controllers\Lk;

use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGeneratorRequest;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Organization;
use App\Models\Payment;
use PhpOffice\PhpWord\TemplateProcessor;


class PaymentGeneratorController extends Controller
{
    public function create()
    {
        // $templatePath = resource_path('templates/test.docx');
        // $templateProcessor = new TemplateProcessor($templatePath);

        // $templateProcessor->setValue('number', '999999');
        // $templateProcessor->setValue('name', 'Иванов Иван');

        // $outputPath = storage_path('\contract.docx');
        // $templateProcessor->saveAs($outputPath);
        // dd($outputPath);

        $organisations = Organization::where('active', 1)->get()->toArray();

        return view('lk.payment.create', [
            'organisations' => $organisations
        ]);
    }

    public function store(PaymentGeneratorRequest $request)
    {
        $data = $request->validated();


        $contract = Contract::create($request->contractData());
        
        $contract->payments()->create($request->paymentData());
        $contract->attachPerformer($request->user()->id, ContractUser::SALLER);
        
        // TODO
        // Временное решение, потом поменять на интеграцию
        Payment::create([
            'value' => $request->paymentData()['value'],
            'status' => Payment::STATUS_CONFIRMATION,
            'order' => 1,
            'inn' => $request->paymentData()['inn'],
        ]);


        // $responseData = DocumentGenerator::generatePaymentDocument($data);

        $responseData = [];
        return back()->with(
            $responseData
        );
        
    }
}
