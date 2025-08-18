<?php

namespace App\Http\Requests\Admin\AdvertisingDepartment;

use App\Models\Global\WorkPlan;
use Illuminate\Foundation\Http\FormRequest;

class AdvertisingWorkPlanRequest extends FormRequest
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
            'type' => 'required|string|in:' . implode(',', WorkPlan::ALL_PLANS),
        ];

        $simplePlans = [
            WorkPlan::B1_PLAN,
            WorkPlan::B2_PLAN,
            WorkPlan::B3_PLAN,
        ];

        $onlyBonusPlans = [
            WorkPlan::UPSALE_BONUS,
        ];

        if ($this->isMethod('POST')) {
            $rules = array_merge($rules, [
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
            ]);
        }

        if (in_array($this->input('type'), $simplePlans)) {
            $rules = array_merge($rules, [
                'data.goal' => 'required|integer',
                'data.bonus' => 'required|numeric',
            ]);
        }

        if (in_array($this->input('type'), $onlyBonusPlans)) {
            $rules = array_merge($rules, [
                'data.goal' => 'nullable|integer',
                'data.bonus' => 'required|numeric',
            ]);
        }

        return $rules;
    }
}
