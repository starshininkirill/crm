<?php

namespace App\Http\Requests;

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
        return [
            // заменить
            'descr' => 'required|exists:contracts,id', 
            'value' => 'required|numeric|min:0',
            'order' => 'nullable|integer|min:0',
            'id_technical' => 'sometimes|boolean',
            'payment_method_id' => 'required|exists:payment_methods,id'
        ];
    }
}
