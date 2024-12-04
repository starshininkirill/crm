<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceStoreRequest;
use App\Http\Requests\ServiceUpdateRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(ServiceStoreRequest $request){

        $validated = $request->validated();

        Service::create($validated);

        return redirect()->back()->with('success', 'Услуга успешно создана');
    }

    
    public function update (ServiceUpdateRequest $request, Service $service)
    {
        $validated = $request->validated();
        
        $service->update($validated);

        return redirect()->back()->with('success', 'Услуга успешно обновлена');
    }
}
