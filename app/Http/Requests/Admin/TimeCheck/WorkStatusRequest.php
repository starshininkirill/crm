<?php

namespace App\Http\Requests\Admin\TimeCheck;

use Illuminate\Foundation\Http\FormRequest;

class WorkStatusRequest extends FormRequest
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
        $rules = [
            'user_id' => 'required|integer|exists:users,id',
            'work_status_id' => 'nullable|integer|exists:work_statuses,id',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i|after_or_equal:date_start',
        ];

        return $rules;
    }
}
