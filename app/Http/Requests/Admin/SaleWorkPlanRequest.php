<?php

namespace App\Http\Requests\Admin;

use App\Models\WorkPlan;
use Illuminate\Foundation\Http\FormRequest;

class SaleWorkPlanRequest extends FormRequest
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

            if ($this->input('type') == WorkPlan::B1_PLAN) {
                $rules = array_merge($rules, [
                    'data.avgDurationCalls' => 'required|integer',
                    'data.avgCountCalls' => 'required|integer',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B2_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.excludeIds' => 'nullable|array',
                    'data.excludeIds.*' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B3_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.includedCategoryIds' => 'required|array',
                    'data.includedCategoryIds.*' => 'integer|exists:services,id',
                    'data.excludeServicePairs' => 'nullable|array',
                    'data.excludeServicePairs.*' => 'array|size:2',
                    'data.excludeServicePairs.*.0' => 'integer|exists:services,id',
                    'data.excludeServicePairs.*.1' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B4_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::MOUNTH_PLAN) {
                $rules = array_merge($rules, [
                    'data.goal' => 'required|integer',
                    'data.month' => 'required|integer',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::DOUBLE_PLAN || $this->input('type') == WorkPlan::WEEK_PLAN) {
                $rules = array_merge($rules, [
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::BONUS_PLAN || $this->input('type') == WorkPlan::SUPER_PLAN || $this->input('type') == WorkPlan::PERCENT_LADDER) {
                $rules = array_merge($rules, [
                    'data.goal' => 'nullable|integer',
                    'data.bonus' => 'required|numeric',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::NO_PERCENTAGE_MONTH) {
                $rules = array_merge($rules, [
                    'data.goal' => 'required|integer',
                ]);

                return $rules;
            }
        }

        if ($this->isMethod('POST')) {
            $rules = array_merge($rules, [
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'nullable|exists:positions,id',
            ]);

            if ($this->input('type') == WorkPlan::B1_PLAN) {
                $rules = array_merge($rules, [
                    'data.avgDurationCalls' => 'required|integer',
                    'data.avgCountCalls' => 'required|integer',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B2_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.excludeIds' => 'nullable|array',
                    'data.excludeIds.*' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B3_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.includedCategoryIds' => 'required|array',
                    'data.includedCategoryIds.*' => 'integer|exists:services,id',
                    'data.excludeServicePairs' => 'nullable|array',
                    'data.excludeServicePairs.*' => 'array|size:2',
                    'data.excludeServicePairs.*.0' => 'integer|exists:services,id',
                    'data.excludeServicePairs.*.1' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::B4_PLAN) {
                $rules = array_merge($rules, [
                    'data.includeIds' => 'required|array',
                    'data.includeIds.*' => 'integer|exists:services,id',
                    'data.goal' => 'required|integer',
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            };

            if ($this->input('type') == WorkPlan::MOUNTH_PLAN) {
                $rules = array_merge($rules, [
                    'data.goal' => 'required|integer',
                    'data.month' => 'required|integer',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::DOUBLE_PLAN || $this->input('type') == WorkPlan::WEEK_PLAN) {
                $rules = array_merge($rules, [
                    'data.bonus' => 'required|integer',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::BONUS_PLAN || $this->input('type') == WorkPlan::SUPER_PLAN) {
                $rules = array_merge($rules, [
                    'data.bonus' => 'required|integer',
                    'data.goal' => 'required|integer',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::PERCENT_LADDER) {
                $rules = array_merge($rules, [
                    'data.goal' => 'nullable|integer',
                    'data.bonus' => 'required|numeric',
                ]);

                return $rules;
            }

            if ($this->input('type') == WorkPlan::NO_PERCENTAGE_MONTH) {
                $rules = array_merge($rules, [
                    'data.goal' => 'required|integer',
                ]);

                return $rules;
            }
        }

        return $rules;
    }
}
