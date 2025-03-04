<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrganizationServiceDocumentTemplateRequest;
use App\Models\OrganizationServiceDocumentTemplate;

class OrganizationServiceDocumentTemplateController extends Controller
{
    public function store(OrganizationServiceDocumentTemplateRequest $request)
    {
        $validated = $request->validated();

        OrganizationServiceDocumentTemplate::create($validated);

        return redirect()->back()->with('success', 'Шаблон документа успешно привязан');
    }

    public function destroy(OrganizationServiceDocumentTemplate $osdt)
    {
        $osdt->delete();

        return redirect()->back()->with('success', 'Шаблон документа успешно отвязан');
    }
}
