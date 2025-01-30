<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationServiceDocumentTemplateRequest;
use App\Models\OrganizationServiceDocumentTemplate;
use Illuminate\Http\Request;

class OrganizationServiceDocumentTemplateController extends Controller
{
    public function store(OrganizationServiceDocumentTemplateRequest $request)
    {
        $validated = $request->validated();

        OrganizationServiceDocumentTemplate::create($validated);

        return redirect()->back()->with('success', 'Успешно привязанно');
    }
}
