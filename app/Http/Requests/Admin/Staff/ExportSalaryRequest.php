<?php

namespace App\Http\Requests\Admin\Staff;

use Illuminate\Foundation\Http\FormRequest;

class ExportSalaryRequest extends FormRequest
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
            'date' => 'required|date',
            'half' => 'required|integer|in:1,2',
            'department_id' => 'nullable|integer|exists:departments,id',
            'employment_type_ids' => 'required|array',
            'employment_type_ids.*' => 'integer|exists:employment_types,id',
        ];
    }
}
