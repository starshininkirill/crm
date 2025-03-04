<?php

namespace App\Http\Controllers\Admin;

use App\Classes\FileManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentTemplateRequest;
use App\Models\DocumentTemplate;
use App\Models\Organization;
use App\Models\OrganizationServiceDocumentTemplate;
use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $documetTemplates = DocumentTemplate::all();

        $documetTemplates = $documetTemplates->map(function ($document) {
            return [
                'id' => $document->id,
                'name' => $document->name,
                'file_path' => Storage::url($document->file),
                'file_name' => basename($document->file),
            ];
        });

        return Inertia::render('Admin/Organization/DocumentTemplate/Index', [
            'documetTemplates' => $documetTemplates,
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

    public function attach()
    {
        $documetTemplates = DocumentTemplate::all();
        $services = Service::all();
        $organizations = Organization::all();

        $osdt = OrganizationServiceDocumentTemplate::with('organization', 'service', 'documentTemplate')->get()->map(function ($osdt) {
            $osdt['type'] = OrganizationServiceDocumentTemplate::translateType($osdt->type);
            return $osdt;
        });

        $osdtTypes = OrganizationServiceDocumentTemplate::visualTypes();

        return Inertia::render('Admin/Organization/DocumentTemplate/Attach', [
            'documetTemplates' => $documetTemplates,
            'services' => $services,
            'organizations' => $organizations,
            'osdt' => $osdt,
            'osdtTypes' => $osdtTypes

        ]);
    }
}
