<?php

namespace App\Http\Controllers\Web\Admin\DocumentGenerator;

use App\Classes\FileManager;
use App\Http\Controllers\Controller;
use App\Http\Filters\Models\DocumentTemplateFilter;
use App\Http\Filters\Models\GeneratedDocumentFilter;
use App\Http\Requests\Admin\DocumentGenerator\DocumentTemplateRequest;
use App\Models\DocumentGeneratorTemplate;
use App\Models\GeneratedDocument;
use App\Models\Option;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DocumentGeneratorController extends Controller
{

    public function generatedDocuments(Request $request, GeneratedDocumentFilter $filter)
    {
        $documents = GeneratedDocument::filter($filter)
            ->latest()
            ->paginate(30)
            ->withQueryString()
            ->through(function ($document) {
                return [
                    'id' => $document->id,
                    'type' => $document->formatedType(),
                    'deal' => $document->deal,
                    'file_name' => $document->file_name,
                    'word_file' => Storage::url($document->word_file),
                    'pdf_file' => $document->pdf_file  ? Storage::url($document->pdf_file) : null,
                    'date' => $document->created_at->format('d.m.Y H:i'),
                    'creater' => $document->creater,
                    'act_number' => $document->act_number,
                    'inn' => $document->inn,
                ];
            });

        $documentsCount = GeneratedDocument::count();

        return Inertia::render('Admin/DocumentTemplate/GeneratedDocuments', [
            'documents' => $documents,
            'filters' => $request->all(),
            'documentsCount' => $documentsCount,
        ]);
    }

    public function index(DocumentTemplateFilter $filter, Request $request)
    {
        $documentTemplates = DocumentGeneratorTemplate::filter($filter)
            ->orderBy('template_id')
            ->paginate(30)
            ->withQueryString()
            ->through(function ($document) {
                return [
                    'id' => $document->id,
                    'result_name' => $document->result_name,
                    'template_id' => $document->template_id,
                    'file_path' => Storage::url($document->file),
                    'file_name' => basename($document->file),
                    'use_custom_doc_number' => $document->use_custom_doc_number,
                ];
            });

        return Inertia::render('Admin/DocumentTemplate/Index', [
            'documentTemplates' => $documentTemplates,
            'filters' => $request->all(),
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
            'result_name' => $validated['result_name'],
            'file' => $path,
            'use_custom_doc_number' => $validated['use_custom_doc_number'],
        ]);

        return redirect()->back()->with('success', 'Шаблон документа успешно создан');
    }

    public function update(DocumentTemplateRequest $request, DocumentGeneratorTemplate $documentTemplate, FileManager $fileManager)
    {
        $validated = $request->validated();

        $documentTemplate->result_name = $validated['result_name'];
        $documentTemplate->template_id = $validated['template_id'];
        $documentTemplate->use_custom_doc_number = $validated['use_custom_doc_number'];
        
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
