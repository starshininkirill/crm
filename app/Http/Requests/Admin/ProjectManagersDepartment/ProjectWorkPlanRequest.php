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

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            if ($this->input('type') == WorkPlan::INDIVID_CATEGORY_IDS || $this->input('type') == WorkPlan::READY_SYTES_CATEGORY_IDS) {
                $rules = array_merge($rules, [
                    'data.categoryIds' => 'required|array',
                    'data.categoryIds.*' => 'integer|exists:service_categories,id',
                ]);
            }

            return $rules;
        } else if ($this->isMethod('POST')) {

            $rules = array_merge($rules, [
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
            ]);


            if ($this->input('type') == WorkPlan::INDIVID_CATEGORY_IDS || $this->input('type') == WorkPlan::READY_SYTES_CATEGORY_IDS) {
                $rules = array_merge($rules, [
                    'data.categoryIds' => 'required|array',
                    'data.categoryIds.*' => 'integer|exists:service_categories,id',
                ]);
            }
        }

        return $rules;
    }
}
