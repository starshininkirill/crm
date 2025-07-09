<?php

namespace App\Http\Requests\Admin\ProjectManagersDepartment;

use App\Models\WorkPlan;
use Illuminate\Foundation\Http\FormRequest;

class ProjectWorkPlanRequest extends FormRequest
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
            WorkPlan::PERCENT_LADDER,
            WorkPlan::HEAD_B1_PLAN,
            WorkPlan::HEAD_B2_PLAN,
            WorkPlan::HEAD_B4_PLAN,
            WorkPlan::HEAD_UPSALE_PLAN,
        ];

        if ($this->isMethod('POST')) {
            $rules = array_merge($rules, [
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
            ]);
        }

        if ($this->input('type') == WorkPlan::INDIVID_CATEGORY_IDS || $this->input('type') == WorkPlan::READY_SYTES_CATEGORY_IDS) {
            $rules = array_merge($rules, [
                'data.categoryIds' => 'required|array',
                'data.categoryIds.*' => 'integer|exists:service_categories,id',
            ]);
        }

        if (in_array($this->input('type'), $simplePlans)) {
            $rules = array_merge($rules, [
                'data.goal' => 'nullable|integer',
                'data.bonus' => 'required|numeric',
            ]);
        }

        if ($this->input('type') == WorkPlan::UPSALE_BONUS) {
            $rules = array_merge($rules, [
                'data.bonus' => 'required|numeric',
            ]);
        }

        if ($this->input('type') == WorkPlan::HEAD_B3_PLAN) {
            $rules = array_merge($rules, [
                'data.penalty' => 'required|numeric',
                'data.bonus' => 'required|numeric',
            ]);
        }

        if ($this->input('type') == WorkPlan::B4_PLAN) {
            $rules = array_merge($rules, [
                'data.projects' => 'required|integer',
                'data.complexes' => 'required|integer',
                'data.bonus' => 'required|numeric',
            ]);
        }

        return $rules;
    }
}
