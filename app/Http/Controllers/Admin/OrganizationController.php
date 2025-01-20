<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Inertia\Inertia;

class OrganizationController extends Controller
{
    public function index(){
        $organizations = Organization::all();

        return Inertia::render('Admin/Organization/Index', [
            'organizations' => $organizations
        ]);
    }

    public function create(){
        return Inertia::render('Admin/Organization/Create');
    }
    
    public function edit(Organization $organization){
        return Inertia::render('Admin/Organization/Edit',[
            'organization' => $organization,
        ]);
    }
}
