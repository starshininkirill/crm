<?php

namespace App\Classes;

use App\Exceptions\Business\BusinessException;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManager
{
    public function uploadDocument(UploadedFile $file)
    {
        $path = $this->upload($file, 'documents');
        return $path;
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
