<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Classes\FileManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organization\DocumentTemplateRequest;
use App\Models\DocumentTemplate;
use App\Models\Organization;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $documentTemplates = DocumentTemplate::all()->load('organization');
        $organizations = Organization::all();

        $documentTemplates = $documentTemplates->map(function ($document) {
            return [
                'id' => $document->id,
                'name' => $document->name,
                'organization' => $document->organization,
                'file_path' => Storage::url($document->file),
                'file_name' => basename($document->file),
            ];
        });

        return Inertia::render('Admin/Organization/DocumentTemplate/Index', [
            'documentTemplates' => $documentTemplates,
            'organizations' => $organizations,
        ]);
    }

    public function edit(DocumentTemplate $documentTemplate)
    {
        $documentTemplate = [
            'id' => $documentTemplate->id,
            'name' => $documentTemplate->name,
            'file' => $documentTemplate->file,
            'file_path' => Storage::url($documentTemplate->file),
            'file_name' => basename($documentTemplate->file),
        ];
        return Inertia::render('Admin/Organization/DocumentTemplate/Edit', [
            'documentTemplate' => $documentTemplate,
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

        DocumentTemplate::create([
            'name' => $validated['name'],
            'file' => $path,
            'organization_id' => $validated['organization_id'],
        ]);

        return redirect()->back()->with('success', 'Шаблон документа успешно создан');
    }

    public function update(DocumentTemplateRequest $request, DocumentTemplate $documentTemplate, FileManager $fileManager)
    {
        $validated = $request->validated();

        $documentTemplate->name = $validated['name'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            if ($documentTemplate->file) {
                $fileManager->delete($documentTemplate->file);
            }

            $path = $fileManager->uploadDocument($file);

            $documentTemplate->file = $path;
        }

        $documentTemplate->save();

        return redirect()->back()->with('success', 'Шаблон документа успешно обновлён');
    }

    public function destroy(DocumentTemplate $documentTemplate, FileManager $fileManager)
    {
        $fileManager->delete($documentTemplate->file);

        $documentTemplate->delete();

        return redirect()->back()->with('success', 'Шаблон документа успешно удалён');
    }
}
