<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(ServiceStoreRequest $request)
    {
        $validated = $request->validated();
    
        $dealTemplateData = [
            'law_default' => $validated['law_default'] ?? null,
            'law_complex' => $validated['law_complex'] ?? null,
            'physic_default' => $validated['physic_default'] ?? null,
            'physic_complex' => $validated['physic_complex'] ?? null,
        ];
    
        unset($validated['law_default'], $validated['law_complex'], $validated['physic_default'], $validated['physic_complex']);
    
        $validated['deal_template_ids'] = json_encode($dealTemplateData);
    
        Service::create($validated);
    
        return redirect()->back()->with('success', 'Услуга успешно создана');
    }

    
    public function update (ServiceUpdateRequest $request, Service $service)
    {
        $validated = $request->validated();
    
        $dealTemplateData = [
            'law_default' => $validated['law_default'] ?? null,
            'law_complex' => $validated['law_complex'] ?? null,
            'physic_default' => $validated['physic_default'] ?? null,
            'physic_complex' => $validated['physic_complex'] ?? null,
        ];
    
        unset($validated['law_default'], $validated['law_complex'], $validated['physic_default'], $validated['physic_complex']);
    
        $validated['deal_template_ids'] = json_encode($dealTemplateData);
        
        $service->update($validated);

        return redirect()->back()->with('success', 'Услуга успешно обновлена');
    }
}
