<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
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
        // TODO
        // Настроить так, чтобы проверка на все услуги была отдельно
        return [
            'service_id' => 'exists:services,id',
            'document_template_id' => 'required|exists:document_templates,id',
            'organization_id' => [
                'required',
                'exists:organizations,id',
                Rule::unique('organization_service_document_template')->where(function ($query) use ($request) {
                    return $query->where('service_id', $request->service_id)
                        ->where('document_template_id', $request->document_template_id);
                }),
            ],
            'type' => 'required',
        ];
    }
}
