<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
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

        return Inertia::render('Admin/Service/Category/Edit',[
            'category' => $serviceCategory,
            'types' => $types,
        ]);
    }
}
