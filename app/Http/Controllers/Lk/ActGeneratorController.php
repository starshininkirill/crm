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


class ActGeneratorController extends Controller
{
    public function create()
    {
        $organisations = Organization::where('active', 1)->get()->toArray();

        return Inertia::render('Lk/Act/Create', [
            'organisations' => $organisations,
        ]);
    }

    public function store(PaymentGeneratorRequest $request)
    {
        $validated = $request->validated();

        $contract = Contract::create($request->contractData());

        $contract->payments()->create($request->paymentData());
        $contract->attachPerformer($request->user()->id, ContractUser::SALLER);

        $organisation = Organization::where('id', $validated['organization_id'])->first();

        $paymentData = $request->paymentData();
        if ($validated['client_type'] == Client::TYPE_LEGAL_ENTITY) {

            // TODO
            // Временное решение, потом поменять на интеграцию
            $payment = Payment::create([
                'value' => $paymentData['value'],
                'status' => Payment::STATUS_WAIT_CONFIRMATION,
                'order' => 1,
                'inn' => $validated['inn'],
                'organization_id' => $validated['organization_id'],
                'description' => $validated['act_payment_goal'],
            ]);

            $linkData = [
                'type' => 'document',
                'link' => DocumentGenerator::generatePaymentDocument($validated)
            ];
        } else if ($validated['client_type'] == Client::TYPE_INDIVIDUAL) {
            $linkData = [
                'type' => 'sbp',
                'link' => DocumentGenerator::generatePaymentLink($validated['amount_summ'], $validated['client_fio'], $validated['number'], $validated['phone'], $organisation->terminal)
            ];
        };

        return back()->with(['success' => 'Документ успешно сгенерирован', 'linkData' => $linkData]);
    }
}
