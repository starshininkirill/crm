<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function store(ServiceCategoryRequest $request)
    {

        $validated = $request->validated();

        ServiceCategory::create($validated);

        return redirect()->back()->with('success', 'Категория успешно создана');
    }

    public function update (ServiceCategoryRequest $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validated();
        
        $serviceCategory->update($validated);

        return redirect()->back()->with('success', 'Категория успешно обновлена');
    }

    public function destroy(ServiceCategory $serviceCategory)
    {
        $serviceCategory->delete();

        return redirect()->back()->with('success', 'Категория услуг успешно удалена');
    }
}
