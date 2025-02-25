<?php

namespace App\Http\Requests\Lk;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class ContractGeneratorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function storeContract(): array
    {
        return [
            'number' => $this->input('number'),
            'fio' => $this->input('contact_fio'),
            'phone' => $this->input('contact_phone'),
            'sale' => $this->input('sale'),
            'amount_price' => $this->input('amount_price'),
        ];
    }

    public function services(): Collection
    {
        $services = collect($this->input('services'))->map(function($service){
            return [
                'service_id' => $service['service_id'],
                'price' => $service['price']
            ];
        });
        return $services;
    }

    public function payments(): array
    {
        return $this->input('payments');
    }

    public function storeClient(): array
    {
        $default = [
            'type' => $this->input('client_type'),
            'tax' => $this->input('tax'),
        ];

        if ($this->input('client_type') == Client::TYPE_INDIVIDUAL) {
            $res = array_merge($default, [
                'fio' => $this->input('client_fio'),
                'passport_series' => $this->input('passport_series'),
                'passport_number' => $this->input('passport_number'),
                'passport_issued' => $this->input('passport_issued'),
                'physical_address' => $this->input('physical_address')
            ]);
        } else {
            $res = array_merge($default, [
                'organization_name' => $this->input('organization_name'),
                'organization_short_name' => $this->input('organization_short_name'),
                'register_number_type' => $this->input('register_number_type'),
                'register_number' => $this->input('register_number'),
                'legal_address' => $this->input('legal_address'),
                'inn' => $this->input('inn'),
                'current_account' => $this->input('current_account'),
                'correspondent_account' => $this->input('correspondent_account'),
                'bank_name' => $this->input('bank_name'),
                'bank_bik' => $this->input('bank_bik'),
            ]);

            if ($this->input('register_number_type') == Client::TAX_OGRN) {
                $res = array_merge($res, [
                    'director_name' => $this->input('director_name'),
                ]);
            }
        }

        return $res;
    }

    protected function prepareForValidation()
    {

        $services = array_filter($this->input('services'), function($service){
            return $service['service_id'] != null && $service['service_id'] != '';
        });

        $this->merge([
            'services' => array_map(fn($service) => ['service_id' => $service['service_id'], 'price' => $service['price'], 'duration' => $service['duration']], $services),
        ]);

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
            $servicesTotal = collect($services)->sum('price');

            $payments = $this->input('payments', []);
            $paymentsTotal = collect($payments)->sum();

            $sale = $this->input('sale', 0);
            $amountPrice = $this->input('amount_price', 0);

            if ($servicesTotal - $sale != $amountPrice) {
                $validator->errors()->add('amount_price', 'Сумма Услуг со Скидкой должна быть равна Общей сумме.');
            }

            if ($paymentsTotal != $amountPrice) {
                $validator->errors()->add('amount_price', 'Сумма Платежей должна быть равна Общей сумме.');
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
        // $rules = [
        //     // Валидация договора
        //     'leed' => 'required',
        //     'number' => 'required|unique:contracts|numeric',
        //     'contact_fio' => 'required|min:4|max:255',
        //     'contact_phone' => 'required|min:4|max:255',
        //     'amount_price' => 'required|numeric',
        //     'sale' => 'nullable|numeric',

        //     'development_time' => 'required|numeric',

        //     // Валидация всех клиентов
        //     'client_type' => 'required|numeric|in:0,1',
        //     'tax' => 'required|numeric',

        //     // Валидация для Физ. лица
        //     'client_fio' => 'required_if:client_type,0|string|min:2|max:255',
        //     'passport_series' => 'required_if:client_type,0',
        //     'passport_number' => 'required_if:client_type,0',
        //     'passport_issued' => 'required_if:client_type,0|string|max:255',
        //     'physical_address' => 'required_if:client_type,0|string|max:255',

        //     // Валидация для Юр. лица
        //     'organization_name' => 'required_if:client_type,1|string|max:255',
        //     'organization_short_name' => 'required_if:client_type,1|string|max:255',
        //     'register_number_type' => 'required_if:client_type,1|integer|in:0,1',
        //     'register_number' => 'required_if:client_type,1|digits_between:1,20',
        //     'legal_address' => 'required_if:client_type,1|string|max:255',
        //     'inn' => 'required_if:client_type,1|digits_between:10,12',
        //     'current_account' => 'required_if:client_type,1|digits:20',
        //     'correspondent_account' => 'required_if:client_type,1|digits:20',
        //     'bank_name' => 'required_if:client_type,1|string|max:255',
        //     'bank_bik' => 'required_if:client_type,1|digits:9',
        //     'act_payment_summ' => 'required_if:client_type,1|integer',
        //     'act_payment_goal' => 'required_if:client_type,1|string|max:255',

        //     // Валидация услуг
        //     'services' => 'required|array|min:1',
        //     'services.*.service_id' => 'required|exists:services,id',
        //     'services.*.price' => 'required|numeric|min:0',
        //     'services.*.duration' => 'required|numeric|min:0',

        //     'seo_pages' => 'nullable|numeric|min:0',
        //     'rk_text' => 'nullable|string|min:0',
        //     'ready_site_link' => 'nullable|string|min:0',
        //     'ready_site_image' => 'nullable|image',

        //     // Валидация платежей
        //     'payments' => 'nullable|array',
        //     'payments.*' => 'nullable|numeric|min:0',

        // ];

        $rules = [
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

            // Валидация услуг
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.price' => 'required|numeric|min:0',
            'services.*.duration' => 'required|numeric|min:0',

            // Дополнительные поля
            'seo_pages' => 'nullable|numeric|min:0',
            'rk_text' => 'nullable|string|min:0',
            'ready_site_link' => 'nullable|string|min:0',
            'ready_site_image' => 'nullable|image',

            // Валидация платежей
            'payments' => 'nullable|array',
            'payments.*' => 'nullable|numeric|min:0',

        ];

        if ($this->input('client_type') == Client::TYPE_INDIVIDUAL) {
            $rules = array_merge($rules, [
                'client_fio' => 'required_if:client_type,0|string|min:2|max:255',
                'passport_series' => 'required_if:client_type,0',
                'passport_number' => 'required_if:client_type,0',
                'passport_issued' => 'required_if:client_type,0|string|max:255',
                'physical_address' => 'required_if:client_type,0|string|max:255',
            ]);
        }

        if ($this->input('client_type') == Client::TYPE_LEGAL_ENTITY) {
            $rules = array_merge($rules, [
                'organization_name' => 'required_if:client_type,1|string|max:255',
                'organization_short_name' => 'required_if:client_type,1|string|max:255',
                'register_number_type' => 'required_if:client_type,1|integer|in:0,1',
                'register_number' => 'required_if:client_type,1',
                'legal_address' => 'required_if:client_type,1|string|max:255',
                'inn' => 'required_if:client_type,1',
                'current_account' => 'required_if:client_type,1',
                'correspondent_account' => 'required_if:client_type,1',
                'bank_name' => 'required_if:client_type,1|string|max:255',
                'bank_bik' => 'required_if:client_type,1',
                'act_payment_summ' => 'required_if:client_type,1|integer',
                'act_payment_goal' => 'required_if:client_type,1|string|max:255',
            ]);
        }

        if ($this->input('client_type') == Client::TYPE_LEGAL_ENTITY && $this->input('register_number_type') == Client::TAX_OGRN) {
            $rules['director_name'] = 'required|string|max:255';
        }

        return $rules;
    }
}
