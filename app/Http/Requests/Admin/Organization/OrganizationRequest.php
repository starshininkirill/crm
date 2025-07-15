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
            'inn' => 'nullable|integer|unique:organizations,inn',
            'nds' => 'required|boolean',
            'terminal' => 'nullable|integer|min:1',
            'has_doc_number' => 'required|boolean',
            'doc_number' => 'nullable|integer|min:1',
            'wiki_id' => 'nullable|integer|min:1',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $organization = $this->route('organization');

            $rules['inn'] =  [
                'nullable',
                'integer',
                Rule::unique('organizations', 'inn')->ignore($organization->id),
            ];
        }

        return $rules;
    }
}
