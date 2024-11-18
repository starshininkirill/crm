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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'leed' => 'required',
            'number' => 'required|unique:contracts|numeric',
            'contact_fio' => 'required|min:4|max:255',
            'contact_phone' => 'required|min:4|max:255',

            'client_type' => 'required|numeric|in:0,1',
            'tax' => 'required|numeric',

            // Валидация для Физ. Лица
            'client_fio' => 'required_if:client_type,0|string|min:2|max:255',
            'passport_series' => 'required_if:client_type,0', 
            'passport_number' => 'required_if:client_type,0',
            'passport_issued' => 'required_if:client_type,0|string|max:255',
            'physical_address' => 'required_if:client_type,0|string|max:255',

        ];
    }
}
 