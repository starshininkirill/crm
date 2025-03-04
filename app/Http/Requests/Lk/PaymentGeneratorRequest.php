<?php

namespace App\Http\Requests\Lk;

use App\Models\Client;
use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest; 

class PaymentGeneratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        if ($this->isMethod('GET')) {
            $rules =  [
                'payment' => 'required|numeric|exists:payments,id'
            ];
        } else if (($this->isMethod('POST') && $this->routeIs('admin.payment.shortlist.attach'))) {
            $rules = [
                'oldPayment' => 'required|numeric|exists:payments,id',
                'newPayment' => 'required|numeric|exists:payments,id'
            ];
        } else if ($this->isMethod('POST') && $this->routeIs('lk.act.store')) {
            $rules = [
                'client_type' => 'required|numeric|in:0,1',
                'service_id' => 'required|numeric|exists:services,id',
                'leed' => 'required|numeric|min:1',
                'number' => 'required|numeric|exists:contracts,number',
                'deal_id' => 'required|numeric|min:1',
                'organization_id' => 'required|numeric|exists:contracts,number',
            ];
            if ($this->input('client_type') == Client::TYPE_INDIVIDUAL) {
                $rules = array_merge($rules, [
                    'amount_summ' => 'required|numeric|min:1',
                    'client_fio' => '|max:255',
                    // 'phone' => 'required|regex:/^\+?\d{10,15}$/'
                    'phone' => 'required'
                ]);
            } elseif ($this->input('client_type') == Client::TYPE_LEGAL_ENTITY) {
                $rules = array_merge($rules, [
                    // 'payment_type' => 'required|numeric',
                    'organization_short_name' => 'required|string|max:255',
                    'legal_address' => 'required|string|max:255',
                    // 'inn' => 'required|digits_between:10,12',
                    'inn' => 'required|numeric',
                    'act_payment_summ' => 'required|integer',
                    'act_payment_goal' => 'required|string|max:255',
                ]);
            }
        }
        return $rules;
    }

    public function contractData(): array
    {
        return [
            'service_id' => $this->input('service_id'),
            'number' => $this->input('number'),
            'amount_price' => $this->input('amount_summ') ?? $this->input('act_payment_summ'),
            'organization_id' => $this->input('organization_id') ?? null,
        ];
    }

    public function paymentData(): array
    {
        return [
            'value' => $this->input('amount_summ') ?? $this->input('act_payment_summ'),
            'inn' => $this->input('inn') ?? null,
        ];
    }
}
