<?php

namespace App\Classes\Documents;

use App\Classes\Bitrix;
use App\Classes\FileManager;
use App\Exceptions\Api\ApiException;
use App\Models\Global\Option;
use App\Models\Documents\DocumentGeneratorTemplate;
use App\Models\Documents\GeneratedDocument;
use App\Models\Finance\Organization;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;

class DocumentGenerator
{
    public function __construct(
        protected FileManager $fileManager
    ) {}

    public function generateDocumentFromApi(array $requestData)
    {
        Log::channel('document_generator')->info('Начало генерации документа' . Carbon::now()->format('d.m.Y H:i:s'));

        $data = $this->formatSourceData($requestData);

        Log::channel('document_generator')->info(
            'Отформатированный запрос на генерацию документа: ' . var_export($data, true)
        );

        $templateId = $this->getTemplateId($data);
        $dealNumber = $this->getDealNumber($data);
        $formatedData = $this->formatApiData($data);
        $documentTemplate = $this->getDocumentTemplate($templateId);
        $actNumber = $this->getActNumber($documentTemplate, $data);

        if (!$this->fileManager->checkExist($documentTemplate->file)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Файла для шаблона документа не существует',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Файла для шаблона документа не существует');
        }

        $formatedData['DocumentCreateTime'] = Carbon::now()->format('d.m.Y');
        $formatedData['DocumentNumber'] = $actNumber;


        $documentType = $this->getDocumentType($data);

        $documentName = $this->getDocumentName($documentTemplate, $data);


        $documentData = [
            'type' => $documentType,
            'deal' => $dealNumber,
            'file_name' => $documentName,
            'act_number' => $actNumber,
            'data' => $formatedData,
            'creater' => array_key_exists('GENERATED_BY', $formatedData) ? $formatedData['GENERATED_BY'] : '',
            'inn' => array_key_exists('UF_CRM_1671028881', $data) ? $data['UF_CRM_1671028881'] : null,
            'template_id' => $templateId,
            'document_generator_template_id' => $documentTemplate->id, 
        ];

        try {
            if (
                array_key_exists('UF_CRM_1739861052', $data) &&
                !empty($data['UF_CRM_1739861052']) &&
                ($date = Carbon::parse($data['UF_CRM_1739861052'], null, true)) &&
                $date->isValid()
            ) {
                $documentData['document_date'] = $date->format('Y-m-d');
            }
        } catch (\Exception $e) {
            $documentData['document_date'] = Carbon::now()->format('Y-m-d');
        }

        return GeneratedDocument::create($documentData);
    }

    private function getActNumber(DocumentGeneratorTemplate $documentTemplate, array $data): int
    {
        if ($documentTemplate->use_custom_doc_number) {
            if (array_key_exists('ip', $data)) {
                $organization = Organization::firstWhere('wiki_id', $data['ip']);
                if ($organization && $organization->has_doc_number) {
                    $number = $organization->doc_number;
                    if ($number && is_integer($number)) {
                        $organization->doc_number = $organization->doc_number + 1;
                        $organization->save();
                        return $number;
                    }
                }
            }
        }
        $option = Option::query()->firstWhere('name', 'document_generator_num');

        if (!$option) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Настройте нумератор',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Настройте нумератор');
        }

        $number = $option->value;
        $option->value = $option->value + 1;
        $option->save();
        return $number;
    }

    private function formatSourceData(array $data): array
    {
        if (empty($data)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Нет данных для генерации документа',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Нет данных для генерации документа');
        }

        if (array_key_exists('crm_fields', $data)) {
            $data = array_merge($data, $data['crm_fields']);
            unset($data['crm_fields']);
        }
        if (array_key_exists('crm_files', $data)) {
            $data = array_merge($data, $data['crm_files']);
            unset($data['crm_files']);
        }
        if (array_key_exists('deal_meta', $data)) {
            unset($data['deal_meta']);
        }

        return $data;
    }

    private function getTemplateId($data): int|string
    {
        $templateId = array_key_exists('template_id', $data) ? $data['template_id'] : null;

        if (!$templateId) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Не передан id шаблона документа',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Не передан id шаблона документа');
        }

        return $templateId;
    }

    private function getDealNumber($data): string|int
    {
        $dealNumber = array_key_exists('UF_CRM_1671028945', $data) ? $data['UF_CRM_1671028945'] : null;

        if (!$dealNumber) {
            $dealNumber = array_key_exists('deal_number', $data) ? $data['deal_number'] : null;
        }

        if (!$dealNumber) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Не передан Номер договора',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Не передан Номер договора');
        }

        return $dealNumber;
    }

    private function getDocumentTemplate($templateId): DocumentGeneratorTemplate
    {
        $documentTemplate = DocumentGeneratorTemplate::firstWhere('template_id', $templateId);

        if (!$documentTemplate) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Шаблона с id: ' . $templateId . ' не существует',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Шаблона с id: ' . $templateId . ' не существует');
        }

        return $documentTemplate;
    }

    private function getDocumentType($data): string|int
    {
        $documentType = GeneratedDocument::TYPE_PAY;

        if (array_key_exists('generator_action', $data)) {
            if ($data['generator_action'] == 'deal') {
                return GeneratedDocument::TYPE_DEAL;
            } else if ($data['generator_action'] == 'act') {
                return GeneratedDocument::TYPE_ACT;
            } else if ($data['generator_action'] == 'invoice') {
                return GeneratedDocument::TYPE_INVOICE;
            }
        }

        return $documentType;
    }

    private function getDocumentName(DocumentGeneratorTemplate $documentTemplate, $data): string
    {
        $dealNumber = $this->getDealNumber($data);
        $documentType = $this->getDocumentType($data);
        $resultName = $documentTemplate->result_name ?? 'неизвестная_услуга';
        $documentName = $resultName . ' по договору№' . $dealNumber;

        if ($documentType == GeneratedDocument::TYPE_ACT || $documentType == GeneratedDocument::TYPE_INVOICE) {
            $resultName = array_key_exists('UF_CRM_1671028759', $data) ? $data['UF_CRM_1671028759'] : "неизвестная организация";
            $documentName = $dealNumber . '_' . $resultName;
        }

        return $documentName;
    }

    private function formatApiData(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $formattedKey = $this->convertToTemplateKey($key);
            $result[$formattedKey] = $value;
        }
        return $result;
    }

    private function convertToTemplateKey(string $key): string
    {
        if (strpos($key, 'UF_CRM_') === 0 || strpos($key, 'PAY_PURPOSE_') === 0 || strpos($key, 'FORMAT') === 0) {
            $parts = explode('_', $key);
            $camelCase = '';
            foreach ($parts as $part) {
                $camelCase .= ucfirst(strtolower($part));
            }
            return $camelCase;
        }
        return $key;
    }


    public function generatePaymentDocument(array $data): string
    {
        $organisation = Organization::find($data['organization_id']);
        if (!$organisation) {
            return '';
        }

        // TODO 
        // Убрать documentTemplates
        $documentTemplate = $organisation->documentTemplates()->first();

        if (!$documentTemplate || !$this->fileManager->checkExist($documentTemplate->file)) {
            return '';
        }

        $filePath = Storage::path('public/' . $documentTemplate->file);

        $templateProcessor = new TemplateProcessor($filePath);

        $templateProcessor->setValue('DocumentNumber', $data['number']);
        $templateProcessor->setValue('DocumentCreateTime', Carbon::now()->format('Y.m.d'));
        $templateProcessor->setValue('act_payment_goal', $data['act_payment_goal']);
        $templateProcessor->setValue('act_payment_summ', $data['act_payment_summ']);
        $templateProcessor->setValue('nds', $data['act_payment_summ'] / 100 * 5);
        $templateProcessor->setValue('organization_short_name', $data['organization_short_name']);
        $templateProcessor->setValue('inn', $data['inn']);
        $templateProcessor->setValue('legal_address', $data['legal_address']);
        $templateProcessor->setValue('inn', $data['inn']);


        $outputRelativePath = 'generatedDocuments/document.docx';

        $templateProcessor->saveAs(storage_path('app/public/' . $outputRelativePath));

        return Storage::url($outputRelativePath);
    }

    public static function generateDealDocument(array $data)
    {
        $result = Bitrix::generateDealDocument($data);

        return $result;
    }

    public function generatePaymentLink(int $amount, string $name, string $desc, string $phone, int $terminal): string
    {
        $target_payment_link = 'https://grampus-studio.ru/oplata-uslug/';

        $payment_query = http_build_query([
            'amount' => $amount,
            'imya' => $name,
            'description' => $desc,
            'phone' => $phone,
            't' => $terminal,
        ]);

        return file_get_contents('https://clck.ru/--?url=' . urlencode($target_payment_link . '?' . $payment_query));
    }
}
