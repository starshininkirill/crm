<?php

namespace App\Classes;

use App\Exceptions\Business\BusinessException;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\TextRun;

class FileManager
{
    public function uploadDocument(UploadedFile $file)
    {
        $path = $this->upload($file, 'documents');
        return $path;
    }

    public function uploadGeneratorDocument(UploadedFile $file)
    {
        if (!in_array($file->getClientOriginalExtension(), ['docx'])) {
            throw new BusinessException('Поддерживаются только файлы формата DOCX');
        }

        $tempFilePath = $file->getRealPath();

        $this->replaceVariablesInDocx($tempFilePath);

        $templateProcessor = new TemplateProcessor($tempFilePath);

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $safeName = $this->generateUniqueFileName($originalName, $extension, 'documentsGeneratorTemplates');

        $path = 'documentsGeneratorTemplates/' . $safeName;
        $templateProcessor->saveAs(storage_path('app/public/' . $path));

        return $path;
    }

    protected function replaceVariablesInDocx($filePath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($filePath) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (strpos($filename, 'word/document.xml') !== false) {
                    $xmlContent = $zip->getFromIndex($i);
                    $modifiedXml = preg_replace('/\{([^}]+)\}/', '\${\1}', $xmlContent);
                    $zip->deleteIndex($i);
                    $zip->addFromString($filename, $modifiedXml);
                    break;
                }
            }
            $zip->close();
        }
    }

    protected function generateUniqueFileName($baseName, $extension, $folder, $counter = 1)
    {
        $name = $baseName . ($counter > 1 ? '_' . $counter : '') . '.' . $extension;

        if (Storage::disk('public')->exists($folder . '/' . $name)) {
            return $this->generateUniqueFileName($baseName, $extension, $folder, $counter + 1);
        }

        return $name;
    }


    public function delete(string $path): void
    {
        if (!$this->checkExist($path)) {
            throw new BusinessException('Файл для удаления не найден');
        }

        if (!Storage::disk('public')->delete($path)) {
            throw new BusinessException('Не удалось удалить файл');
        };
    }

    public function checkExist(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }

    public function upload(UploadedFile $file, string $folder): string
    {
        try {
            $originalName = $file->getClientOriginalName();
            $safeName = preg_replace('/[\/\\\?%#&<>+|":*]/', '_', $originalName);

            $i = 1;
            while (Storage::disk('public')->exists($folder . '/' . $safeName)) {
                $safeName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    . '_' . $i . '.' . $file->getClientOriginalExtension();
                $i++;
            }

            $path = $file->storeAs($folder, $safeName, 'public');
        } catch (Exception $e) {
            $path = '';
        }

        return $path;
    }
}
