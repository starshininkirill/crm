<?php

namespace App\Services\DocumentTemplateServices;

use App\Exceptions\Business\BusinessException;
use App\Models\DocumentSelectionRule;

class DocumentTemplateService
{
    public function findDocumentTemplateByServices(array $serviceIds)
    {
        $documentRule = $this->findRuleByServices($serviceIds);

        if (!$documentRule) {
            throw new BusinessException('Шаблона документа не найдено');
        }

        return $documentRule->documentTemplate;
    }

    public function findRuleByServices(array $serviceIds)
    {
        $fullMatchQuery = DocumentSelectionRule::query();

        foreach ($serviceIds as $serviceId) {
            $fullMatchQuery->whereHas('services', function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            });
        }

        $fullMatch = $fullMatchQuery->get();

        if ($fullMatch->isNotEmpty()) {
            return $fullMatch->sortByDesc('priority')->first();
        }

        return DocumentSelectionRule::whereHas('services', function ($query) use ($serviceIds) {
            $query->whereIn('service_id', $serviceIds);
        })->get()->sortByDesc('priority')->first();
    }
}
