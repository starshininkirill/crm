<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Inertia\Inertia;

class ServiceCategoryController extends Controller
{
    public function index()
    {

        $categories = ServiceCategory::withCount('services')->get();

        $types = ServiceCategory::getUnusedTypes();

        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->readableType(),
                'services_count' => $category->services_count,
            ];
        });

        return Inertia::render('Admin/Service/Category/Index', [
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        $types = ServiceCategory::getTypes();

        return Inertia::render('Admin/Service/Category/Edit', [
            'category' => $serviceCategory,
            'types' => $types,
        ]);
    }

    public function store(ServiceCategoryRequest $request)
    {

        $validated = $request->validated();

        ServiceCategory::create($validated);

        return redirect()->back()->with('success', 'Категория успешно создана');
    }

    public function update(ServiceCategoryRequest $request, ServiceCategory $serviceCategory)
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
