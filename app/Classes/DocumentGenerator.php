<?php

namespace App\Classes;

use App\Exceptions\Api\ApiException;
use App\Exceptions\Business\BusinessException;
use App\Models\Client;
use App\Models\Option;
use App\Models\Service;
use App\Helpers\TextFormaterHelper;
use App\Models\DocumentGeneratorTemplate;
use App\Models\Organization;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;

class DocumentGenerator
{

    public function __construct(
        protected FileManager $fileManager
    ) {}
    // public static function generatePaymentDocument(array $data)
    // {
    //     $result = [
    //         'link' => '',
    //         'download_link' => '',
    //         'pdf_download_link' => '',
    //     ];

    //     if ($data['client_type'] == Client::TYPE_INDIVIDUAL) {
    //         $paymentDirection = $data['payment_direction'];

    //         if ($paymentDirection == 0) {
    //             $terminal = Organization::where('nds', Organization::WITH_NDS)->first()->terminal;
    //         } else {
    //             $terminal = 1;
    //         }

    //         $link = self::generatePaymentLink(
    //             $data['amount_summ'],
    //             $data['client_fio'],
    //             $data['number'],
    //             $data['phone'],
    //             $terminal
    //         );

    //         $result['link'] = $link;
    //     } elseif ($data['client_type'] == Client::TYPE_LEGAL_ENTITY) { 
    //         $bitrixResponse = Bitrix::generatePaymentDocument($data);

    //         if(array_key_exists('download_link', $bitrixResponse) && $bitrixResponse['download_link'] != ''){
    //             $result['download_link'] = $bitrixResponse['download_link'];
    //         }
    //         if(array_key_exists('pdf_download_link', $bitrixResponse) && $bitrixResponse['pdf_download_link'] != ''){
    //             $result['pdf_download_link'] = $bitrixResponse['pdf_download_link'];
    //         }
    //     }

    //     return $result;
    // }

    public function generateDocumentFromApi(array $data)
    {
        $option = Option::query()->firstWhere('name', 'document_generator_num');


        if (array_key_exists('crm_fields', $data)) {
            $data = array_merge($data, $data['crm_fields']);
            unset($data['crm_fields']);
        }
        if (array_key_exists('crm_files', $data)) {
            $data = array_merge($data, $data['crm_files']);
            unset($data['crm_files']);
        }

        if (!$option) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Настройте нумератор',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Настройте нумератор');
        }

        if (empty($data)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Нет данных для генерации документа',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Нет данных для генерации документа');
        }

        $templateId = array_key_exists('template_id', $data) ? $data['template_id'] : null;

        if (!$templateId) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Не передан id шаблона документа',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Не передан id шаблона документа');
        }

        $dealNumber = array_key_exists('UF_CRM_1671028945', $data) ? $data['UF_CRM_1671028945'] : null;

        if (!$dealNumber) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Не передан Номер договора',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Не передан Номер договора');
        }

        $formatedData = $this->formatApiData($data);

        $documentTemplate = DocumentGeneratorTemplate::firstWhere('template_id', $templateId);

        if (!$documentTemplate) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Шаблона с id: ' . $templateId . ' не существует',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Шаблона с id: ' . $templateId . ' не существует');
        }

        $filePath = Storage::path('public/' . $documentTemplate->file);

        if (!$documentTemplate || !$this->fileManager->checkExist($documentTemplate->file)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Файла для шаблона документа не существует',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Файла для шаблона документа не существует');
        }

        $formatedData['DocumentCreateTime'] = Carbon::now()->format('d.m.Y');
        $formatedData['DocumentNumber'] = $option->value;

        $templateProcessor = new TemplateProcessor($filePath);

        $processedKeys = [];

        foreach ($formatedData as $key => $value) {
            $templateKey = $this->convertToTemplateKey($key);
            $processedKeys[] = $templateKey;

            if ($this->isBase64Image($value)) {
                $this->processImage($templateProcessor, $templateKey, $value);
            } else {
                $templateProcessor->setValue($templateKey, $value);
            }
        }

        $this->removeUnusedVariables($templateProcessor, $processedKeys);

        $mainService = array_key_exists('service_name', $data) ? $data['service_name'] : 'неизвестная_услуга';
        $documentName = $dealNumber . '-' . $mainService;

        $outputRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'docx', 'generatedDocuments');

        $templateProcessor->saveAs(storage_path('app/public/' .  $outputRelativePath));

        $option->value = intval($option->value) + 1;
        $option->save();

        return Storage::url($outputRelativePath);
    }

    private function removeUnusedVariables(TemplateProcessor $processor, array $processedKeys): void
    {
        $variables = $processor->getVariables();

        foreach ($variables as $variable) {
            if (!in_array($variable, $processedKeys)) {
                $processor->setValue($variable, '');
            }
        }
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
        if (strpos($key, 'UF_CRM_') === 0) {
            $parts = explode('_', $key);
            $camelCase = '';
            foreach ($parts as $part) {
                $camelCase .= ucfirst(strtolower($part));
            }
            return $camelCase;
        }
        return $key;
    }

    private function isBase64Image(string|null $data): bool
    {
        if (!$data) {
            return false;
        }
        return strpos($data, 'data:image/') === 0 && strpos($data, 'base64,') !== false;
    }

    private function processImage(TemplateProcessor $processor, string $key, string $base64): void
    {
        $tempImagePath = $this->saveBase64Image($base64);

        try {
            $processor->setImageValue($key, [
                'path'    => $tempImagePath,
                'width'   => 550,
                'height'  => 400,
                'ratio'   => true,
            ]);
        } finally {
            if (file_exists($tempImagePath)) {
                unlink($tempImagePath);
            }
        }
    }

    private function saveBase64Image(string $base64): string
    {
        $parts = explode(',', $base64);
        $imageData = base64_decode($parts[1]);

        $mimeType = explode(';', explode(':', $parts[0])[1])[0];
        $extension = explode('/', $mimeType)[1];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowedExtensions)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Неподдерживаемый формат изображения',
            ]);
            throw new ApiException(Response::HTTP_BAD_REQUEST, 'Неподдерживаемый формат изображения');
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'img_') . '.' . $extension;
        file_put_contents($tempPath, $imageData);

        return $tempPath;
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
