<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Models\DocumentSelectionRule;
use App\Models\DocumentTemplate;
use App\Models\Organization;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentSelectionRuleController extends Controller
{
    public function index()
    {
        $documetTemplates = DocumentTemplate::all();
        $services = Service::all();
        $organizations = Organization::all();

        // $osdt = OrganizationServiceDocumentTemplate::with('organization', 'service', 'documentTemplate')->get()->map(function ($osdt) {
        //     $osdt['type'] = OrganizationServiceDocumentTemplate::translateType($osdt->type);
        //     return $osdt;
        // });

        $documentRuleTypes = DocumentSelectionRule::visualTypes();

        return Inertia::render('Admin/Organization/DocumentTemplateRule/Index', [
            'documetTemplates' => $documetTemplates,
            'services' => $services,
            'organizations' => $organizations,
            // 'osdt' => $osdt,
            'documentRuleTypes' => $documentRuleTypes
        ]);
    }
}
