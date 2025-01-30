<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use App\Models\Organization;
use App\Models\Service;
use Illuminate\Http\Request;
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
                'file_path' => $document->file,
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
            'file_path' => $documentTemplate->file,
            'file_name' => basename($documentTemplate->file),
        ];
        return Inertia::render('Admin/Organization/DocumentTemplate/Edit', [
            'documentTemplate' => $documentTemplate,
        ]);
    }

    public function attach()
    {
        $documetTemplates = DocumentTemplate::all();
        $services = Service::all();
        $organizations = Organization::all();

        return Inertia::render('Admin/Organization/DocumentTemplate/Attach',[
            'documetTemplates' => $documetTemplates,
            'services' => $services,
            'organizations' => $organizations,
        ]);
    }
}
