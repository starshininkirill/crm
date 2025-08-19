<?php

namespace App\Classes\Documents;

use App\Classes\FileManager;
use App\Models\Documents\GeneratedDocument;
use Illuminate\Support\Facades\Storage;


use App\Exceptions\Api\ApiException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\Element\TextRun;


class DocumentFileGenerator
{
    public function __construct(
        protected FileManager $fileManager
    ) {}

    public function generateFile(GeneratedDocument $generatedDocument, string $type, bool $withSign = true)
    {
        $existingFile = $this->checkExistingFiles($generatedDocument, $type, $withSign);
        $withPdf = $type == 'pdf';

        if ($existingFile) {
            return $existingFile;
        }

        if (!$this->fileManager->checkExist($generatedDocument?->documentTemplate->file)) {
            Log::channel('document_generator_errors')->error('Document generation API error', [
                'message' => 'Файла для шаблона документа не существует',
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Файла для шаблона документа не существует');
        }


        $filePath = Storage::path('public/' . $generatedDocument->template->file);

        $$formatedData = $generatedDocument->data;
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
                    if (is_string($value) && strpos($value, '|') !== false) {
                        $textRun = new TextRun();
                        $parts = explode('|', $value);
                        $partsCount = count($parts);
                        foreach ($parts as $index => $part) {
                            $textRun->addText(htmlspecialchars(trim($part)), ['name' => 'Times New Roman', 'size' => 9]);
                            if ($index < $partsCount - 1) {
                                $textRun->addTextBreak();
                            }
                        }
                        $templateProcessor->setComplexValue($templateKey, $textRun);
                    } else {
                        $templateProcessor->setValue($templateKey, $value);
                    }
                }
            }
        }

        $this->removeUnusedVariables($templateProcessor, $processedKeys);


        $docxRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'docx', 'generatedDocuments');
        $docxFullPath = storage_path('app/public/' . $docxRelativePath);
        $templateProcessor->saveAs($docxRelativePath);

        if (!$withPdf) {
            if (!$withSign) {
                $this->removeImagesWithoutAltText($docxFullPath);
            }

            // return url(Storage::url($docxRelativePath));
            return $docxFullPath;
        }

        $pdfRelativePath = 'generatedDocuments/' . $this->fileManager->generateUniqueFileName($documentName, 'pdf', 'generatedDocuments');
        $pdfFullPath = storage_path('app/public/' . $pdfRelativePath);

        try {
            $this->convertDocxToPdf($docxFullPath, $pdfFullPath);
        } catch (\Exception $e) {
            Log::channel('document_generator_errors')->error('Ошибка создания PDF', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new ApiException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Ошибка при конвертации в PDF: ' . $e->getMessage());
        }

        if (!$withSign) {
            $this->removeImagesWithoutAltText($docxFullPath);
        }

        // return url(Storage::url($pdfRelativePath));
        return $pdfFullPath;
    }

    private function checkExistingFiles(GeneratedDocument $generatedDocument, string $type, bool $withSign = true)
    {
        // Если у generatedDocument существует  word_file или pdf_file, то используем этот файл
        // Такая логика будет для старых типов документов
    }

    private function removeImagesWithoutAltText(string $docxPath): void
    {
        $zip = new \ZipArchive();

        if ($zip->open($docxPath) === true) {
            // Загружаем основной документ
            $xml = $zip->getFromName('word/document.xml');

            if ($xml === false) {
                $zip->close();
                throw new \Exception("Не удалось открыть document.xml в $docxPath");
            }

            // Загружаем в DOM для обработки
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = false;
            $doc->formatOutput = true;
            libxml_use_internal_errors(true);
            $doc->loadXML($xml);
            libxml_clear_errors();

            $xpath = new \DOMXPath($doc);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $xpath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');

            // Находим все <wp:docPr> (описания картинок)
            $nodes = $xpath->query("//wp:docPr");

            foreach ($nodes as $node) {
                $descr = $node->getAttribute('descr');

                // Если атрибут пустой → удаляем картинку
                if (empty($descr)) {
                    $drawing = $node->parentNode->parentNode;
                    if ($drawing && $drawing->parentNode) {
                        $drawing->parentNode->removeChild($drawing);
                    }
                }
            }

            // Сохраняем изменённый XML обратно в zip
            $zip->addFromString('word/document.xml', $doc->saveXML());
            $zip->close();
        } else {
            throw new \Exception("Не удалось открыть DOCX как архив: $docxPath");
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

        if (!unlink($docxPath)) {
            throw new \Exception("Не удалось удалить исходный DOCX файл: $docxPath");
        }
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

    private function removeUnusedVariables(TemplateProcessor $processor, array $processedKeys): void
    {
        $variables = $processor->getVariables();

        foreach ($variables as $variable) {
            if (!in_array($variable, $processedKeys)) {
                $processor->setValue($variable, '');
            }
        }
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
}
