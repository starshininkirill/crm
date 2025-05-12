<?php

namespace App\Http\Requests\Admin\Organization;

use App\Models\DocumentSelectionRule;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Http\FormRequest;

class DocumentSelectionRuleRequest extends FormRequest
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
        if ($this->method() == 'POST') {
            $rules = array_merge($rules, [
                'document_template_id' => 'required|exists:document_templates,id',
                'type' => 'required|in:' . implode(',', DocumentSelectionRule::types()),
                'services' => 'required|array',
                'services.*' => 'required|exists:services,id',
            ]);
        } else if ($this->method() == 'GET') {
            $rules = array_merge($rules, [
                'services' => 'required|array',
                'services.*' => 'required|exists:services,id',
            ]);
        }

        return $rules;
    }

    public function documentSelectedRule(): array
    {
        return [
            'document_template_id' => $this->get('document_template_id'),
            'organization_id' => $this->get('organization_id'),
            'type' => $this->get('type'),
        ];
    }

    public function services(): array
    {
        return $this->get('services');
    }
}
