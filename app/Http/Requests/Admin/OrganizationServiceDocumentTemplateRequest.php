<?php

namespace App\Http\Requests\Admin;

use App\Rules\UniqueOrganizationServiceDocumentTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request; 

class OrganizationServiceDocumentTemplateRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'service_id' => 'nullable|exists:services,id',
            'document_template_id' => 'required|exists:document_templates,id',
            'organization_id' => [
                'required',
                'exists:organizations,id',
                new UniqueOrganizationServiceDocumentTemplate($request->service_id, $request->document_template_id, $request->organization_id),
            ],
            'type' => 'required',
        ];
    }
}
