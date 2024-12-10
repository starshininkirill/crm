<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
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
        $rules = [
            'client_type' => 'required|numeric|in:0,1',
            'payment_direction' => 'required|numeric|in:0,1,2,3,',
            'leed' => 'required|numeric|min:1',
            'number' => 'required|numeric|min:1',
            'deal_id' => 'required|numeric|min:1'
        ];
        if ($this->input('client_type') == Client::TYPE_INDIVIDUAL) {
            $rules = array_merge($rules, [
                'amount_summ' => 'required|numeric|min:1',
                'client_fio' => '|max:255',
                // 'phone' => 'required|regex:/^\+?\d{10,15}$/'
                'phone' => 'required'
            ]);
        } elseif ($this->input('client_type') == Client::TYPE_LEGAL_ENTITY) {
        }
        return $rules;
    }
}
