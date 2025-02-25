<?php

namespace App\Http\Requests\Lk;

use Illuminate\Foundation\Http\FormRequest;

class SbpGeneratorRequest extends FormRequest
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
            'number' => 'required|exists:contracts,number',
            'value' => 'required|numeric|',
            'description' => 'required|min:3|max:255|string',
            'date' => 'required|date_format:Y-m-d\TH:i',
            'organization_id' => 'required|exists:organizations,id',
            'file' => 'required|image'
        ];
    }
}
