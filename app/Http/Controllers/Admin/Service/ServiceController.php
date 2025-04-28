<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Http\Filters\Models\ServiceFilter;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request, ServiceFilter $filter)
    {
        $services = Service::with('category')
            ->filter($filter)
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString()
            ->through(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'category' => $service->category ? $service->category->only('id', 'name') : null,
                ];
            });

        $categories = ServiceCategory::all(['id', 'name'])->toArray();

        return Inertia::render('Admin/Service/Index', [
            'services' => $services,
            'categories' => $categories,
            'filters' => $request->all(),
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

    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        Service::create($validated);

        return redirect()->back()->with('success', 'Услуга успешно создана');
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $validated = $request->validated();

        $service->update($validated);

        return redirect()->back()->with('success', 'Услуга успешно обновлена');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->back()->with('success', 'Услуга успешно Удалена');
    }
}
