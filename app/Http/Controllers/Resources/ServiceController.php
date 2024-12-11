<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(ServiceRequest $request)
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

    
    public function update (ServiceRequest $request, Service $service)
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

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->back()->with('success', 'Услуга успешно Удалена');
    }
}
