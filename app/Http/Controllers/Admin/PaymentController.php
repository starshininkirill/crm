<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\Business\BusinessException;
use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use App\Services\ContractService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('contract')->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'created_at' => $payment->created_at->format('H:i d.m.Y'),
                'contract' => $payment->contract()->with('client')->first(),
                'value' => TextFormaterHelper::getPrice($payment->value),
                'status' => $payment->status,
                'formatStatus' => $payment->getStatusNameAttribute(),
            ];
        });

        return Inertia::render('Admin/Payment/Index', [
            'payments' => $payments,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }

    public function show(Payment $payment)
    {
        return Inertia::render('Admin/Payment/Show', [
            'payment' => [
                'id' => $payment->id,
                'contract' => $payment->contract,
                'value' => $payment->value,
                'status' => $payment->status,
                'formatStatus' => $payment->getStatusNameAttribute(),
                'inn' => $payment->inn,
                'type' => $payment->formatedType() != '' ? $payment->formatedType() : 'Не определён',
                'is_technical' => $payment->is_technical,
                'confirmed_at' => $payment->confirmed_at != null ? $payment->confirmed_at->format('d.m.Y H:i') : 'Не подтвержён',
                'created_at' => $payment->created_at->format('d.m.Y H:i'),
                'responsible' => $payment->responsible,
                'organization' => $payment->organization,
            ],
            'paymentStatuses' => Payment::vueStatuses(),
            'paymentTypes' => Payment::ASOC_TYPES,
            'organizations' => Organization::all(),
            'users' => User::all()
        ]);
    }

    public function update(PaymentRequest $request, Payment $payment, PaymentService $service)
    {
        $validated = $request->validated();

        $isUpdated = $service->update($payment, $validated);

        if (!$isUpdated) {
            throw new BusinessException('Не удалось обновить платёж');
        }

        return redirect()->back()->with('success', 'Платёж успешно обновлён');
    }

    public function unsorted(ContractService $service)
    {
        $payments = Payment::query()->whereNull('contract_id')
            ->where('status', Payment::STATUS_WAIT_CONFIRMATION)
            ->whereNotNull('inn')
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'created_at' => $payment->created_at->format('Y.m.d H:i '),
                'value' => TextFormaterHelper::getPrice($payment->value),
                'inn' => $payment->inn,
                'organization' => $payment->organization,
                'description' => $payment->description,
            ];
        });

        return Inertia::render('Admin/Payment/Unsorted', [
            'payments' => $payments,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }

    public function unsortedSbp()
    {
        $payments = Payment::query()->whereNull('contract_id')
            ->where('status', Payment::STATUS_WAIT_CONFIRMATION)
            ->whereNull('inn')
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'created_at' => $payment->created_at->format('d.m.Y H:i '),
                'value' => TextFormaterHelper::getPrice($payment->value),
                'inn' => $payment->inn,
                'organization' => $payment->organization,
                'description' => $payment->description,
                'receipt_url' => $payment->receipt_url,
            ];
        });

        return Inertia::render('Admin/Payment/UnsortedSbp', [
            'payments' => $payments,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }

    public function searchContract(Request $request, ContractService $service)
    {

        if (!$request->get('s')) {
            throw new BusinessException('Поисковой запрос не найден');
        }
        return $service->searchContract($request->get('s'));
    }

    public function shortlistAttach(PaymentRequest $request, PaymentService $service)
    {
        $validated = $request->validated();
        $oldPayment = Payment::find($validated['oldPayment']);
        $newPayment = Payment::find($validated['newPayment']);
        $user = auth()->user();

        $service->attachPayment($newPayment, $oldPayment, $user);

        return redirect()->back()->with('success', 'Платёж успешно привязан');
    }

    public function shortlist(Payment $payment, PaymentService $service): array
    {
        $payments = $service->getShortlist($payment);

        return $payments;
    }
}
