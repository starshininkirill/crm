<?php

namespace App\Http\Requests;

use App\Models\WorkPlan;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkPlanRequest extends FormRequest
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
    public function rules(): array
    {
        $rules = [
            'type' => ['required', 'integer', 'in:' . implode(',', WorkPlan::ALL_PLANS)],
            'goal' => ['nullable', 'numeric', 'min:0'],
            'mounth' => ['nullable', 'integer', 'min:1'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'service_category_id' => ['nullable', 'exists:service_categories,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ];

        return $rules;
    }
}
