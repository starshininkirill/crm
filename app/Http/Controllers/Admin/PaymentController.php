<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        // $payments = Payment::query()->where('status', Payment::STATUS_CLOSE)->get();
        // $payments = Payment::whereNot('status', Payment::STATUS_WAIT_CONFIRMATION)->orderBy('created_at', 'asc')->get();
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

    public function unsorted()
    {
        $payments = Payment::query()->whereNull('contract_id')
            ->where('status', Payment::STATUS_WAIT_CONFIRMATION)
            ->whereNotNull('inn')
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'created_at' => $payment->created_at->format('d.m.Y H:i '),
                'value' => TextFormaterHelper::getPrice($payment->value),
                'inn' => $payment->inn,
                'organization'=> $payment->organization,
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
                'organization'=> $payment->organization,
                'description' => $payment->description,
                'receipt_url' => $payment->receipt_url,
            ];
        });

        return Inertia::render('Admin/Payment/UnsortedSbp', [
            'payments' => $payments,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }

    public function show(Payment $payment)
    {
        $payment->load('contract');
        $payment->load('responsible');

        return Inertia::render('Admin/Payment/Show', [
            'payment' => [
                'id' => $payment->id,
                'contract' => $payment->contract,
                'value' => TextFormaterHelper::getPrice($payment->value),
                'status' => $payment->status,
                'formatStatus' => $payment->getStatusNameAttribute(),
                'type' => $payment->formatedType() != '' ? $payment->formatedType() : 'Не определён',
                'method' => $payment->generetePaymentMethodHierarchy() != '' ? $payment->generetePaymentMethodHierarchy() : 'Не определён',
                'is_technical' => $payment->is_technical,
                'confirmed_at' => $payment->confirmed_at != null ? $payment->confirmed_at->format('d.m.Y H:i') : 'Не подтвержён',
                'created_at' => $payment->created_at->format('d.m.Y H:i'),
                'responsible' => $payment->responsible,
            ],
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }
}
