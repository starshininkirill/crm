<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryStoreRequest;
use App\Http\Requests\ServiceCategoryUpdateRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function store(ServiceCategoryStoreRequest $request)
    {

        $validated = $request->validated();

        ServiceCategory::create($validated);

        return redirect()->back()->with('success', 'Категория успешно создана');
    }

    public function update (ServiceCategoryUpdateRequest $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validated();
        
        $serviceCategory->update($validated);

        return redirect()->back()->with('success', 'Категория успешно обновлена');
    }
}
