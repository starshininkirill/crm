<?php

namespace App\Http\Controllers\Resources;

use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('s')) {
            $contract = Contract::where('number', $request->get('s'))->first();
            if ($contract) {
                return [
                    'contract' => [
                        'id' => $contract->id,
                        'number' => $contract->number,
                        'created_at' => $contract->created_at->format('Y.m.d'),
                        'client_name' => $contract->getClientName(),
                        'inn' => $contract->getInn(),
                        'amount_price' => TextFormaterHelper::getPrice($contract->amount_price),
                        'services' => $contract->services ?? [],
                        'payments' => $contract->payments->map(function ($payment) {
                            return [
                                'id' => $payment->id,
                                'value' => TextFormaterHelper::getPrice($payment->value),
                                'close' => $payment->status == Payment::STATUS_CLOSE,
                                'order' => $payment->order
                            ];
                        }),
                        'childs' => $contract->childs->map(function ($subContract) {
                            return [
                                'id' => $subContract->id,
                                'number' => $subContract->number,
                                'created_at' => $subContract->created_at->format('Y.m.d'),
                                'amount_price' => TextFormaterHelper::getPrice($subContract->amount_price),
                                'payments' => $subContract->payments->map(function ($payment) {
                                    return [
                                        'id' => $payment->id,
                                        'value' => TextFormaterHelper::getPrice($payment->value),
                                        'close' => $payment->status == Payment::STATUS_CLOSE,
                                        'order' => $payment->order
                                    ];
                                }),
                            ];
                        })
                    ],
                ];
            }
        }

        return [
            'error' => 'Договор не найден'
        ];
    }
}
