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
        ];

        if ($this->routeIs('admin.time-check.handle-work-status')) {
            $rules = array_merge($rules, [
                'work_status_id' => 'nullable|integer|exists:work_statuses,id',
                'date' => 'required|date_format:Y-m-d',
                'time_start' => 'nullable|date_format:H:i',
                'time_end' => 'nullable|date_format:H:i|after_or_equal:date_start',
            ]);
        } elseif ($this->routeIs('admin.time-check.handle-mass-update')) {
            $rules = array_merge($rules, [
                'work_status_id' => 'nullable|integer|exists:work_statuses,id',
                'dates' => 'required|array',
                'dates.*' => 'required|date_format:Y-m-d',
            ]);
        } elseif ($this->routeIs('admin.time-check.close-sick-leave')) {
            $rules = array_merge($rules, [
                'dates' => 'required|array',
                'dates.*' => 'required|date_format:Y-m-d',
                'image' => 'required|image',
            ]);
        } elseif ($this->routeIs('admin.time-check.reject-late')) {
            $rules = array_merge($rules, [
                'date' => 'required|date_format:Y-m-d',
            ]);
        }

        return $rules;
    }
}
