<?php

namespace App\Http\Requests;

use App\Models\WorkPlan;
use Illuminate\Foundation\Http\FormRequest;

class WorkPlanRequest extends FormRequest
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

        if ($this->input('type') == WorkPlan::MOUNTH_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['mounth'] = ['required', 'integer', 'min:1'];
        };

        if ($this->input('type') == WorkPlan::BONUS_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };

        if ($this->input('type') == WorkPlan::DOUBLE_PLAN) {
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };

        if ($this->input('type') == WorkPlan::WEEK_PLAN) {
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };

        if ($this->input('type') == WorkPlan::SUPER_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };

        if ($this->input('type') == WorkPlan::B1_PLAN || $this->input('type') == WorkPlan::B2_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };

        if ($this->input('type') == WorkPlan::B3_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };
        
        if ($this->input('type') == WorkPlan::B4_PLAN) {
            $rules['goal'] = ['required', 'numeric', 'min:0'];  
            $rules['bonus'] = ['required', 'numeric', 'min:0'];
        };
        

        return $rules;
    }
    
    public function updateData() : array
    {
        return $this->only([
            'goal',
            'bonus',
            'mounth',
        ]);
    }
}
