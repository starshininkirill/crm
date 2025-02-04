<?php

namespace App\Http\Controllers\Lk;

use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGeneratorRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\ContractUser;
use App\Models\Organization;
use App\Models\OrganizationServiceDocumentTemplate;
use App\Models\Payment;
use Inertia\Inertia;
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

        return Inertia::render('Lk/Payment/Create', [
            'organisations' => $organisations,
        ]);

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

        $documentLink = DocumentGenerator::generatePaymentDocument($data);

        // TODO
        // Временное решение, потом поменять на интеграцию
        $paymentData = $request->paymentData();
        if ($data['client_type'] == Client::TYPE_LEGAL_ENTITY) {

            $payment = Payment::create([
                'value' => $paymentData['value'],
                'status' => Payment::STATUS_WAIT_CONFIRMATION,
                'order' => 1,
                'inn' => $paymentData['inn'],
                'organization_id' => $paymentData['organization_id'],
                'description' => $paymentData['act_payment_goal'],
            ]);
        }
        
        return back()->with(['success' => 'Документ успешно сгенерирован', 'link' => $documentLink]);
    }
}
