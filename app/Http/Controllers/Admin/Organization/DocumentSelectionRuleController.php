<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Exceptions\Business\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organization\DocumentSelectionRuleRequest;
use App\Models\DocumentSelectionRule;
use App\Models\DocumentTemplate;
use App\Models\Organization;
use App\Models\Service;
use App\Services\DocumentTemplateServices\DocumentTemplateService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DocumentSelectionRuleController extends Controller
{
    public function index()
    {
        $documentTemplates = DocumentTemplate::all();
        $services = Service::all();
        $organizations = Organization::all();

        $documentRules = DocumentSelectionRule::with('services', 'documentTemplate.organization')->get()->map(function ($rule) {
            $rule['type'] = DocumentSelectionRule::translateType($rule->type);
            return $rule;
        });


        $documentRuleTypes = DocumentSelectionRule::visualTypes();

        return Inertia::render('Admin/Organization/DocumentTemplateRule/Index', [
            'documentTemplates' => $documentTemplates,
            'services' => $services,
            'organizations' => $organizations,
            'documentRules' => $documentRules,
            'documentRuleTypes' => $documentRuleTypes
        ]);
    }

    public function store(DocumentSelectionRuleRequest $request)
    {
        DB::beginTransaction();

        try {
            $rule = DocumentSelectionRule::create($request->documentSelectedRule());

            if (!$rule) {
                throw new BusinessException('Не удалось создать правило');
            }

            $rule->services()->sync($request->services());

            DB::commit();

            return redirect()->back()->with('success', 'Правило успешно создано');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException('Не удалось создать правило');
        }
    }

    public function destroy(DocumentSelectionRule $documentSelectionRule)
    {
        DB::beginTransaction();

        try {
            $documentSelectionRule->services()->detach();

            if (!$documentSelectionRule->delete()) {
                throw new BusinessException('Не удалось удалить правило');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BusinessException('Не удалось удалить правило');
        }
    }

    public function check(DocumentSelectionRuleRequest $request, DocumentTemplateService $service)
    {
        $validated = $request->validated();

        $serviceIds = $validated['services'];
        
        $document = $service->findDocumentTemplateByServices($serviceIds);
        
        dd($document);
    }
}
