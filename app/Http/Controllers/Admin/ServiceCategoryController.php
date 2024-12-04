<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index()
    {

        $categories = ServiceCategory::withCount('services')->get();

        $types = ServiceCategory::getUnusedTypes();

        return view('admin.service.category.index', [
            'categories' => $categories,
            'types' => $types
        ]);
    }

    public function edit(ServiceCategory $serviceCategory)
    {
        $types = ServiceCategory::getTypes();

        return view('admin.service.category.edit', [
            'serviceCategory' => $serviceCategory,
            'types' => $types
        ]);
    }
}
