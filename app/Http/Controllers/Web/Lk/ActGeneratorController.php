<?php

namespace App\Http\Controllers\Web\Lk;

use App\Classes\DocumentGenerator;
use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lk\PaymentGeneratorRequest;
use App\Models\Contracts\Client;
use App\Models\Contracts\Contract;
use App\Models\Contracts\ContractUser;
use App\Models\Global\Option;
use App\Models\Finance\Organization;
use App\Models\Finance\Payment;
use App\Models\Services\Service;
use App\Services\ContractService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class ActGeneratorController extends Controller
{
    public function create()
    {
        $organisations = Organization::get()->toArray();
        $serviceIdsOption = Option::whereName('payment_generator_services')->first();

        if (!$serviceIdsOption) {
            throw new BusinessException('Не выбраны Технические услуги для генератора');
        }

        $serviceIds = json_decode($serviceIdsOption->value);
        
        if (empty($serviceIds)) {
            throw new BusinessException('Не выбраны Технические услуги для генератора');
        }
        
        $services = Service::whereIn('id', $serviceIds)->get();

        if ($services->isEmpty()) {
            throw new BusinessException('Не выбраны Технические услуги для генератора');
        }

        return Inertia::render('Lk/Act/Create', [
            'organisations' => $organisations,
            'services' => $services,
        ]);
    }

    public function store(PaymentGeneratorRequest $request, ContractService $service, DocumentGenerator $documentGenerator)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        $contract = $service->storeSubContract($request->contractData());

        $contract->payments()->create($request->paymentData());

        $contract->users()->attach(auth()->user()->id, [
            'role' => ContractUser::SELLER,
        ]);

        $organisation = Organization::find($validated['organization_id']);

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
                'operation_id' =>  mt_rand(100000, 1000000),
            ]);

            // $generatedDocumentData = $documentGenerator->generatePaymentDocument($validated);
            $generatedDocumentData = true;

            if (!$generatedDocumentData) {
                DB::rollBack();
                return back()->withErrors('Не удалось сгенерировать документ');
            }

            $linkData = [
                'type' => 'document',
                'link' => $generatedDocumentData
            ];
        } else if ($validated['client_type'] == Client::TYPE_INDIVIDUAL) {
            $linkData = [
                'type' => 'sbp',
                'link' => $documentGenerator->generatePaymentLink($validated['amount_summ'], $validated['client_fio'], $validated['number'], $validated['phone'], $organisation->terminal)
            ];
        };

        DB::commit();
        return back()->with(['success' => 'Документ успешно сгенерирован', 'linkData' => $linkData]);
    }
}
