<?php

namespace App\Http\Controllers\Web\Admin\DocumentGenerator;

use App\Classes\FileManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentGenerator\DocumentTemplateRequest;
use App\Models\DocumentGeneratorTemplate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentGeneratorController extends Controller
{
    public function index()
    {
        $documentTemplates = DocumentGeneratorTemplate::all();

        $documentTemplates = $documentTemplates->map(function ($document) {
            return [
                'id' => $document->id,
                'template_id' => $document->template_id,
                'file_path' => Storage::url($document->file),
                'file_name' => basename($document->file),
            ];
        });

        return Inertia::render('Admin/DocumentTemplate/Index', [
            'documentTemplates' => $documentTemplates,
        ]);
    }

    public function store(DocumentTemplateRequest $request, FileManager $fileManager)
    {
        $validated = $request->validated();

        if (!$request->hasFile('file')) {
            return back()->withErrors('Не удалось создать Шаблон документа');
        }

        $file = $request->file('file');

        $path = $fileManager->uploadDocument($file);

        DocumentGeneratorTemplate::create([
            'template_id' => $validated['template_id'],
            'file' => $path,
        ]);

        return redirect()->back()->with('success', 'Шаблон документа успешно создан');
    }

    public function destroy(DocumentGeneratorTemplate $documentTemplate, FileManager $fileManager)
    {
        dd($documentTemplate);
        $fileManager->delete($documentTemplate->file);

        $documentTemplate->delete();

        return redirect()->back()->with('success', 'Шаблон документа успешно удалён');
    }
}
