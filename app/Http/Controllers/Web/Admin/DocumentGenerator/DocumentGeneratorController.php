<?php

namespace App\Http\Controllers\Web\Admin\DocumentGenerator;

use App\Classes\FileManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentGenerator\DocumentTemplateRequest;
use App\Models\DocumentGeneratorTemplate;
use App\Models\Option;
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
                'name' => $document->name,
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

        $path = $fileManager->uploadGeneratorDocument($file);

        DocumentGeneratorTemplate::create([
            'template_id' => $validated['template_id'],
            'name' => $validated['name'],
            'file' => $path,
        ]);

        return redirect()->back()->with('success', 'Шаблон документа успешно создан');
    }

    public function update(DocumentTemplateRequest $request, DocumentGeneratorTemplate $documentTemplate, FileManager $fileManager)
    {
        $validated = $request->validated();

        $documentTemplate->name = $validated['name'];
        $documentTemplate->template_id = $validated['template_id'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if ($documentTemplate->file) {
                $fileManager->delete($documentTemplate->file);
            }

            $path = $fileManager->uploadGeneratorDocument($file);

            $documentTemplate->file = $path;
        }

        $documentTemplate->save();

        return redirect()->back()->with('success', 'Шаблон документа успешно обновлён');
    }

    public function destroy(DocumentGeneratorTemplate $documentTemplate, FileManager $fileManager)
    {
        $fileManager->delete($documentTemplate->file);

        $documentTemplate->delete();

        return redirect()->back()->with('success', 'Шаблон документа успешно удалён');
    }

    public function nums()
    {
        $option = Option::query()->firstWhere('name', 'document_generator_num');

        return Inertia::render('Admin/DocumentTemplate/Nums', [
            'option' => $option
        ]);
    }
}
