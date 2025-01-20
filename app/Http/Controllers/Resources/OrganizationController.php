<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{

    public function store(OrganizationRequest $request)
    {
        $validated = $request->validated();

        Organization::create($validated);

        return redirect()->back()->with('success', 'Организация успешно создана');
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->back()->with('success', 'Организация успешно удалена');
    }
}
