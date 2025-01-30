<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentTemplateRequest;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentTemplateController extends Controller
{
    public function store(DocumentTemplateRequest $request)
    {
        $validated = $request->validated();

        if (!$request->hasFile('file')) {
            return back()->withErrors('Не удалось создать Шаблон документа');
        }

        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();

        $i = 1;
        while (Storage::exists('documents/' . $originalName)) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                . '_' . $i . '.' . $file->getClientOriginalExtension();
            $i++;
        }

        $path = $file->storeAs('documents', $originalName);

        DocumentTemplate::create([
            'name' => $validated['name'],
            'file' => $path,
        ]);

        return redirect()->back()->with('success', 'Шаблон документа успешно создан');
    }

    public function update(DocumentTemplateRequest $request, DocumentTemplate $documentTemplate)
    {
        $validated = $request->validated();

        $documentTemplate->name = $validated['name'];

        if ($request->hasFile('file')) {
            $oldFile = $documentTemplate->file;

            $file = $request->file('file');

            if ($documentTemplate->file && Storage::exists($documentTemplate->file)) {
                Storage::delete($documentTemplate->file);
            }

            $originalName = $file->getClientOriginalName();
            $i = 1;
            while (Storage::exists('documents/' . $originalName)) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                    . '_' . $i . '.' . $file->getClientOriginalExtension();
                $i++;
            }

            $path = $file->storeAs('documents', $originalName);
            $documentTemplate->file = $path;

            Storage::delete($oldFile);
        }

        $documentTemplate->save();

        return redirect()->back()->with('success', 'Шаблон документа успешно обновлён');
    }

    public function destroy(DocumentTemplate $documentTemplate)
    {
        Storage::delete($documentTemplate->file);

        $documentTemplate->delete();

        return redirect()->back()->with('success', 'Шаблон документа успешно удалён');
    }

    public function download(DocumentTemplate $documentTemplate)
    {
        $filePath = storage_path('app/' . $documentTemplate->file);

        if (!file_exists($filePath) || $documentTemplate->file == null) {
            return back()->withErrors('Не удалось получить файл на скачивание');
        }

        return response()->download($filePath);
    }
}
