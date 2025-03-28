<?php

namespace App\Http\Requests\Lk;

use Illuminate\Foundation\Http\FormRequest;

class OverworkRequest extends FormRequest
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
            'hours' => 'required|integer',
            'date' => 'required|date|date_format:Y-m-d',
            'description' => 'nullable'
        ];
    }
}
