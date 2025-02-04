<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueOrganizationServiceDocumentTemplate implements Rule
{
    protected $serviceId;
    protected $documentTemplateId;
    protected $organizationId;

    public function __construct($serviceId, $documentTemplateId, $organizationId)
    {
        $this->serviceId = $serviceId;
        $this->documentTemplateId = $documentTemplateId;
        $this->organizationId = $organizationId;
    }

    public function passes($attribute, $value)
    {
        return !DB::table('organization_service_document_template')
            ->where('organization_id', $value)
            ->where('service_id', $this->serviceId)
            ->where('document_template_id', $this->documentTemplateId)
            ->exists();
    }

    public function message()
    {
        return 'Комбинация Организации, Услуги и Шаблона документа должна быть уникальна';
    }
}