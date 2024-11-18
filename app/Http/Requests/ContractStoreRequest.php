<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation()
    {
        if ($this->has(['service', 'service_price'])) {
            $services = array_map(null, $this->input('service', []), $this->input('service_price', []));
            $this->merge([
                'services' => array_map(fn($pair) => ['service_id' => $pair[0], 'price' => $pair[1]], $services),
            ]);
        }

        if ($this->has('payments')) {
            $filteredPayments = array_filter($this->input('payments', []), fn($payment) => $payment > 0);
            $this->merge([
                'payments' => array_values($filteredPayments),
            ]);
        }
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $services = $this->input('services', []);
            $amountPrice = $this->input('amount_price', 0);
            $sale = $this->input('sale', 0);

            $servicesTotal = collect($services)->sum('price');

            if ($servicesTotal - $sale != $amountPrice) {
                $validator->errors()->add('amount_price', 'Сумма Услуг со Скидкой должна быть равна Общей сумме.');
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Валидация договора
            'leed' => 'required',
            'number' => 'required|unique:contracts|numeric',
            'contact_fio' => 'required|min:4|max:255',
            'contact_phone' => 'required|min:4|max:255',
            'amount_price' => 'required|numeric',
            'sale' => 'nullable|numeric',

            'development_time' => 'required|numeric',

            // Валидация всех клиентов
            'client_type' => 'required|numeric|in:0,1',
            'tax' => 'required|numeric',

            // Валидация для Физ. лица
            'client_fio' => 'required_if:client_type,0|string|min:2|max:255',
            'passport_series' => 'required_if:client_type,0',
            'passport_number' => 'required_if:client_type,0',
            'passport_issued' => 'required_if:client_type,0|string|max:255',
            'physical_address' => 'required_if:client_type,0|string|max:255',

            // Валидация для Юр. лица
            'organization_name' => 'required_if:client_type,1|string|max:255',
            'organization_short_name' => 'required_if:client_type,1|string|max:255',
            'register_number_type' => 'required_if:client_type,1|integer|in:1,2,3',
            'register_number' => 'required_if:client_type,1|digits_between:1,20',
            'director_name' => 'required_if:client_type,1|string|max:255',
            'legal_address' => 'required_if:client_type,1|string|max:255',
            'inn' => 'required_if:client_type,1|digits_between:10,12',
            'current_account' => 'required_if:client_type,1|digits:20',
            'correspondent_account' => 'required_if:client_type,1|digits:20',
            'bank_name' => 'required_if:client_type,1|string|max:255',
            'bank_bik' => 'required_if:client_type,1|digits:9',
            'act_payment_summ' => 'required_if:client_type,1|integer',
            'act_payment_goal' => 'required_if:client_type,1|string|max:255',

            // Валидация услуг
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.price' => 'required|numeric|min:0',

            // Валидация платежей
            'payments' => 'nullable|array',
            'payments.*' => 'nullable|numeric|min:0',

        ];
    }
}
