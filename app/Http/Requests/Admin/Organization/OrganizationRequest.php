<?php

namespace App\Http\Requests\Admin\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationRequest extends FormRequest
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
            'short_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'inn' => 'required|integer|unique:organizations,inn',
            'nds' => 'required|integer|in:0,1',
            'terminal' => 'required|integer|min:1',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $organization = $this->route('organization');

            $rules['inn'] =  [
                'required',
                'integer',
                Rule::unique('organizations', 'inn')->ignore($organization->id),
            ];
        }

        return $rules;
    }
}
