<?php

namespace App\Http\Controllers\Resources;

use App\Classes\Bitrix;
use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentRequest;
use App\Models\Contract;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function shortlistAttach(PaymentRequest $request)
    {
        $validated = $request->validated();
        $oldPayment = Payment::find($validated['oldPayment']);
        $newPayment = Payment::find($validated['newPayment']);

        try {
            DB::beginTransaction();

            $oldPayment->value = $newPayment->value;
            $oldPayment->inn = $newPayment->inn;
            $oldPayment->status = Payment::STATUS_CLOSE;
            $oldPayment->organization_id = $newPayment->organization_id;
            $oldPayment->confirmed_at = Date::now();
            $oldPayment->responsible_id = $request->user()->id;
            $oldPayment->operation_id = $newPayment->operation_id ?? null;
            $oldPayment->type = $oldPayment->determineType($newPayment);

            $newPayment->delete();
            $oldPayment->save();

            DB::commit();
        } catch (Exception $exeption) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Ошибка прикрепления платежа']);
        }

        return redirect()->back()->with('success', 'Платёж успешно привязан');
    }

    public function shortlist(Payment $payment): array
    {
        $payments = collect();

        $contract = Contract::query()
            ->whereHas('client', function ($query) use ($payment) {
                $query->where('inn', $payment->inn);
            })
            ->first();
        if ($contract) {
            $payments = $contract->payments()->where('status', Payment::STATUS_WAIT)->get();
            if ($contract->childs) {
                foreach ($contract->childs as $child) {
                    $payments = $payments->merge($child->payments()->where('status', Payment::STATUS_WAIT)->get());
                }
            }
        }


        if (!$payments->isEmpty()) {
            $payments = $payments->map(function ($payment) {
                $data =  [
                    'id' => $payment->id,
                    'value' => TextFormaterHelper::getPrice($payment->value),
                    'contract' => $payment->contract,
                    'order' => $payment->order,
                ];

                if ($payment->contract->parent) {
                    $data['inn'] = $payment->contract->parent->client->inn;
                } else {
                    $data['inn'] = $payment->contract->client->inn;
                };

                return $data;
            });
        }

        return $payments->toArray();
    }

    public function store(PaymentRequest $request)
    {
        $validated = $request->validated();

        $payment = Payment::created($validated);
        
        return redirect()->back()->with('success', 'Платёж успешно создан');
        
        // Bitrix::generatePaymentDocument($data);
    }
}
