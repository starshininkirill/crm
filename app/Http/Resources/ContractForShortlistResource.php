<?php

namespace App\Http\Resources;

use App\Helpers\TextFormaterHelper;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractForShortlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'created_at' => $this->created_at->format('Y.m.d'),
            'client_name' => $this->getClientName(),
            'inn' => $this->getInn(),
            'amount_price' => TextFormaterHelper::getPrice($this->amount_price),
            'services' => $this->services ?? [],
            'payments' => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'value' => TextFormaterHelper::getPrice($payment->value),
                    'close' => $payment->status == Payment::STATUS_CLOSE,
                    'order' => $payment->order
                ];
            }),
            'childs' => $this->childs->map(function ($subContract) {
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
        ];
    }
}
