<?php

namespace App\Http\Controllers\Admin\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Organization\OrganizationRequest;
use App\Models\Organization;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::all();

        return Inertia::render('Admin/Organization/Index', [
            'organizations' => $organizations
        ]);
    }


    public function edit(Organization $organization)
    {
        return Inertia::render('Admin/Organization/Edit', [
            'organization' => $organization,
        ]);
    }

    public function store(OrganizationRequest $request)
    {
        $validated = $request->validated();

        Organization::create($validated);

        return redirect()->back()->with('success', 'Организация успешно создана');
    }

    public function update(OrganizationRequest $request, Organization $organization)
    {
        $validated = $request->validated();

        $organization->update($validated);

        return redirect()->back()->with('success', 'Организация успешно обновлена');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->back()->with('success', 'Организация успешно удалена');
    }
}
