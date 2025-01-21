<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Service;
use App\Models\ServiceCategory;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index($serviceCategoryId = null)
    {

        if ($serviceCategoryId) {
            $services = Service::where('service_category_id', $serviceCategoryId)->get();
        } else {
            $services = Service::all();
        };

        $services = $services->sortBy('name')->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'category' => $service->category->only('id', 'name')
            ];
        });

        return Inertia::render('Admin/Service/Index', [
            'services' => $services->sortBy('name')->values()->toArray(),
        ]);
    }

    public function create()
    {
        $categories = ServiceCategory::all(['id', 'name'])->toArray();

        return Inertia::render('Admin/Service/Create', [
            'categories' => $categories,
        ]);

    }

    public function edit(Service $service)
    {
        $categories = ServiceCategory::all();

        return Inertia::render('Admin/Service/Edit', [
            'service' => $service,
            'categories' => $categories,
        ]);
    }
}
