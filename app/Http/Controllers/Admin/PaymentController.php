<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        // $payments = Payment::query()->where('status', Payment::STATUS_CLOSE)->get();
        // $payments = Payment::whereNot('status', Payment::STATUS_CONFIRMATION)->orderBy('created_at', 'asc')->get();
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
            ->whereNot('status', Payment::STATUS_CLOSE)
            ->get();

        $payments = $payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'created_at' => $payment->created_at->format('H:i d.m.Y'),
                'value' => TextFormaterHelper::getPrice($payment->value),
                'inn' => $payment->inn,
            ];
        });

        return Inertia::render('Admin/Payment/Unsorted', [
            'payments' => $payments,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);

        return view('admin.payment.unsorted', ['payments' => $payments]);
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
