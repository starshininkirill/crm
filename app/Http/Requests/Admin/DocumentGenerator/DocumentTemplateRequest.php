<?php

namespace App\Http\Requests\Admin\DocumentGenerator;

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
                'template_id' => 'required|integer',
                'file' => 'required|file',
            ]);
        }
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $documentTemplate = $this->route('documentTemplate');

            $rules = array_merge($rules, [
                'template_id' => 'required|integer',
                'file' => 'nullable|file',
            ]);
        }

        return $rules;
    }
}
