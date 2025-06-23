<?php

namespace App\Classes;

use App\Exceptions\Api\ApiException;
use App\Models\Option;
use App\Models\DocumentGeneratorTemplate;
use App\Models\DocumentTemplate;
use App\Models\GeneratedDocument;
use App\Models\Organization;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\TextRun;

class DocumentGenerator
{

    public function __construct(
        protected FileManager $fileManager
    ) {}

    public function generateDocumentFromApi(array $requestData)
    {
        Log::channel('document_generator')->info('Начало генерации документа' . Carbon::now()->format('d.m.Y H:i:s'));
        $option = $this->getOption();

        $data = $this->formatSourceData($requestData);

        Log::channel('document_generator')->info(
            'Отформатированный запрос на генерацию документа: ' . var_export($data, true)
        );

        $templateId = $this->getTemplateId($data);

        $dealNumber = $this->getDealNumber($data);

        $formatedData = $this->formatApiData($data);

        $documentTemplate = $this->getDocumentTemplate($templateId);

        $filePath = Storage::path('public/' . $documentTemplate->file);

        if (!$this->fileManager->checkExist($documentTemplate->file)) {
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
            if (is_array($value)) {
                continue;
            }
            $templateKey = $this->convertToTemplateKey($key);
            $processedKeys[] = $templateKey;

            if ($this->isBase64Image($value)) {
                $this->processImage($templateProcessor, $templateKey, $value);
            } else {
                if ($templateKey == 'UfCrm1671029036' && $value != '') {
                    $this->insertFormattedHtml($templateProcessor, $templateKey, $value);
                } else {
                    $templateProcessor->setValue($templateKey, $value);
                }
            }
        }

        $this->removeUnusedVariables($templateProcessor, $processedKeys);

        $documentType = $this->getDocumentType($data);

        $documentName = $this->getDocumentName($documentTemplate, $data);

        $docxRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'docx', 'generatedDocuments');
        $docxFullPath = storage_path('app/public/' . $docxRelativePath);
        $templateProcessor->saveAs($docxFullPath);

        $pdfRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'pdf', 'generatedDocuments');
        $pdfFullPath = storage_path('app/public/' . $pdfRelativePath);

        $withPdf = array_key_exists('with_pdf', $data) ? boolval($data['with_pdf']) : false;

        if ($withPdf) {
            try {
                $this->convertDocxToPdf($docxFullPath, $pdfFullPath);
            } catch (\Exception $e) {
                Log::channel('document_generator_errors')->error('Ошибка создания PDF', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Ошибка при конвертации в PDF: ' . $e->getMessage());
            }
        }

        $this->incrementOption($option);

        $generatedDocument = GeneratedDocument::create([
            'type' => $documentType,
            'deal' => $dealNumber,
            'file_name' => $documentName,
            'word_file' => $docxRelativePath,
            'pdf_file' => $withPdf ? $pdfRelativePath : null,
        ]);

        if (!$generatedDocument) {
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Не удалось записать договор в БД');
        }
        Log::channel('document_generator')->info('Конец генерации документа' . Carbon::now()->format('d.m.Y H:i:s'));

        return [
            'download_link' => url(Storage::url($docxRelativePath)),
            'pdf_download_link' => $withPdf ? url(Storage::url($pdfRelativePath)) : '',
        ];
    }

    private function getOption(): Option
    {
        $option = Option::query()->firstWhere('name', 'document_generator_num');

        if (!$option) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Настройте нумератор',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Настройте нумератор');
        }

        return $option;
    }

    private function incrementOption(Option $option): void
    {
        $option->value = intval($option->value) + 1;
        $option->save();
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

        if ($documentType == GeneratedDocument::TYPE_ACT) {
            $resultName = array_key_exists('UF_CRM_1671028759', $data) ? $data['UF_CRM_1671028759'] : "неизвестная организация";
            $documentName = $dealNumber . '_' . $resultName;
        }

        return $documentName;
    }

    private function saveDOXC(TemplateProcessor $templateProcessor, string $documentName): string
    {
        $docxRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'docx', 'generatedDocuments');
        $docxFullPath = storage_path('app/public/' . $docxRelativePath);
        $templateProcessor->saveAs($docxFullPath);

        return $docxFullPath;
    }

    private function insertFormattedHtml(TemplateProcessor $templateProcessor, string $placeholder, string $html)
    {
        // Удаляем <p>, преобразуем <br> в переносы строк, обрабатываем <strong>
        $doc = new \DOMDocument();
        // Для загрузки HTML без ошибок
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        libxml_clear_errors();

        $body = $doc->getElementsByTagName('body')->item(0);

        $textRun = new TextRun();

        // Рекурсивно обходим DOM и строим текст с форматированием
        $this->parseNode($body, $textRun);

        // Заменяем в шаблоне сложным блоком
        $templateProcessor->setComplexBlock($placeholder, $textRun);
    }

    private function parseNode(\DOMNode $node, TextRun $textRun)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType == XML_TEXT_NODE) {
                // Обычный текст
                $textRun->addText($child->textContent, ['name' => 'Times New Roman', 'size' => 10]);
            } elseif ($child->nodeType == XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);

                if ($tag === 'strong' || $tag === 'b') {
                    // Добавляем жирный текст
                    $textRun->addText($child->textContent, ['bold' => true, 'name' => 'Times New Roman', 'size' => 10]);
                } elseif ($tag === 'br') {
                    // Перенос строки
                    $textRun->addTextBreak();
                } elseif ($tag === 'p') {
                    // Параграф: рекурсивно обрабатываем содержимое и добавляем перенос в конце
                    $this->parseNode($child, $textRun);
                    $textRun->addTextBreak();
                } else {
                    // Другие теги: рекурсивно
                    $this->parseNode($child, $textRun);
                }
            }
        }
    }


    private function convertDocxToPdf(string $docxPath, string $pdfPath): void
    {
        // Проверка существования исходного файла
        if (!file_exists($docxPath)) {
            throw new \Exception("Исходный DOCX файл не существует: $docxPath");
        }

        // Команда для конвертации через LibreOffice
        $command = sprintf(
            'libreoffice --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg(dirname($pdfPath)),
            escapeshellarg($docxPath)
        );

        // Выполнение команды
        exec($command, $output, $returnCode);

        // Проверка успешности выполнения
        if ($returnCode !== 0) {
            throw new \Exception(sprintf(
                "Ошибка конвертации DOCX в PDF (код %d): %s",
                $returnCode,
                implode("\n", $output)
            ));
        }

        // Формирование ожидаемого пути к PDF
        $expectedPdfPath = dirname($docxPath) . '/' . pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';

        // Проверка создания PDF
        if (!file_exists($expectedPdfPath)) {
            throw new \Exception("PDF файл не был создан: $expectedPdfPath");
        }

        // Перемещаем файл в указанное место
        if (!rename($expectedPdfPath, $pdfPath)) {
            throw new \Exception("Не удалось переместить PDF в указанную директорию");
        }
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
        if (strpos($key, 'UF_CRM_') === 0 || strpos($key, 'PAY_PURPOSE_') === 0) {
            $parts = explode('_', $key);
            $camelCase = '';
            foreach ($parts as $part) {
                $camelCase .= ucfirst(strtolower($part));
            }
            return $camelCase;
        }
        return $key;
    }

    private function isBase64Image($data): bool
    {
        if (!$data || is_array($data) || !is_string($data)) {
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
                'height'  => 335,
                'ratio'   => false,
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
