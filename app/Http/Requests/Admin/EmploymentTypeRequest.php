<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmploymentTypeRequest extends FormRequest
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
            'name' => 'required|string|min:4|max:255',
            'compensation' => 'required|numeric|between:0,100',
            'fields' => 'nullable|array',
            'fields.*.name' => 'required|string|min:1|max:255|regex:/^[a-zA-Z0-9_\-]+$/',
            'fields.*.readName' => 'required|string|min:1|max:255|regex:/^[а-яА-ЯёЁ\s]+$/u',
            'fields.*.type' => 'required|string|in:text,number',
        ];
    }
}
