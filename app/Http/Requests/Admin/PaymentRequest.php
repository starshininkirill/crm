<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
        $rules = [];
        if ($this->isMethod('GET')) {
            $rules =  [
                'payment' => 'required|numeric|exists:payments,id'
            ];
        } else if (($this->isMethod('POST') && $this->routeIs('payment.shortlist.attach'))) {
            $rules = [
                'oldPayment' => 'required|numeric|exists:payments,id',
                'newPayment' => 'required|numeric|exists:payments,id'
            ];
        }
        return $rules;
    }
}
