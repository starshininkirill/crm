<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    public function getClientData() : array
    {
        return array_filter([
            'name' => $this->input('client_name'),
            'city' => $this->input('client_city'),
            'email' => $this->input('client_email'),
            'phone' => $this->input('client_phone'),
            'company' => $this->input('client_company'),
            'inn' => $this->input('client_inn'),
        ]);
    }

    public function storeContract(Client $client): array
    {
        $data = $this->only([
            'number',
            'amount_price',
            'comment',
        ]);
        
        return array_merge(
            $data,
            [
                'client_id' => $client->id,
            ]
        );
    }
    
    public function rules(): array
    {

        $default = [
            'number' => 'required|string|max:255|unique:contracts,number',
            'amount_price' => 'required|numeric|min:0',
            'comment' => 'nullable|string|max:1000',
            'service' => 'required|array',
            'service.*' => 'exists:services,id',
            'payments' => 'nullable|array',
            'payments.*' => 'nullable|numeric|min:0',
        ];
        
        if($this->getRequestUri() == route('admin.contract.store', [], false))
        {
            return array_merge($default, [
                'client_name' => 'required|min:3|max:255',
                'client_city' => 'nullable|string|max:255',
                'client_email' => 'nullable|email|max:255',
                'client_phone' => 'nullable|string|max:20',
                'client_company' => 'nullable|string|max:255',
                'client_inn' => 'nullable|string|regex:/^\d{1,}$/',
            ]);
        }

        return [];

    }

    public function messages(): array
    {
        return [
            'number.required' => 'Поле "номер договора" обязательно для заполнения.',
            'number.unique' => 'Договор с таким номером уже существует.',
            'client_name.required' => 'Поле "имя клиента" обязательно для заполнения.',
            'client_name.min' => 'Поле "имя клиента" должно содержать минимум :min символа.',
            'client_name.max' => 'Поле "имя клиента" должно содержать максимум :max символов.',
            'client_city.max' => 'Поле "город клиента" должно содержать максимум :max символов.',
            'client_email.email' => 'Поле "email клиента" должно содержать корректный адрес электронной почты.',
            'client_email.max' => 'Поле "email клиента" должно содержать максимум :max символов.',
            'client_phone.max' => 'Поле "телефон клиента" должно содержать максимум :max символов.',
            'client_company.max' => 'Поле "название компании клиента" должно содержать максимум :max символов.',
            'client_inn.regex' => 'Поле "ИНН клиента" должно содержать только цифры.',
            'amount_price.required' => 'Поле "общая стоимость" обязательно для заполнения.',
            'amount_price.numeric' => 'Поле "общая стоимость" должно быть числом.',
            'amount_price.min' => 'Поле "общая стоимость" не может быть меньше :min.',
            'payments.array' => 'Поле "платежи" должно быть массивом.',
            'payments.*.numeric' => 'Каждый платеж должен быть числом.',
            'payments.*.min' => 'Каждый платеж не может быть меньше :min.',
            'service.required' => 'Поле "услуги" обязательно для заполнения.',
            'service.array' => 'Поле "услуги" должно быть массивом.',
            'service.*.exists' => 'Выбрана недопустимая услуга.',
            'comment.max' => 'Поле "комментарий" должно содержать максимум :max символов.',
        ];
    }
}
