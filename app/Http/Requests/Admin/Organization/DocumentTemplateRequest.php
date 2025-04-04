<?php

namespace App\Http\Requests\Admin\Organization;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentTemplateRequest extends FormRequest
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
        $rules = [];
        if ($this->isMethod('POST')) {
            $rules = array_merge($rules, [
                'name' => 'required|min:2|max:255|unique:document_templates|',
                'file' => 'required|file', 
                'organization_id' => 'required|exists:organizations,id',
            ]);
        }
        if($this->isMethod('PUT') || $this->isMethod('PATCH')){
            $documentTemplate = $this->route('documentTemplate');


            $rules = array_merge($rules, [
                'name' => [
                    'required',
                    'min:2',
                    'max:255',
                    Rule::unique('document_templates', 'name')->ignore($documentTemplate->id), 
                ],
                'file' => 'nullable|file',
            ]);
        }

        return $rules;
    }
}
