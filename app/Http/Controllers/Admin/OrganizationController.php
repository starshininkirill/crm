<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(){
        $organizations = Organization::all();

        return view('admin.organization.index', [
            'organizations' => $organizations,
        ]);
    }
    public function create(){
        return view('admin.organization.create');
    }
    public function edit(){
        return view('admin.organization.create');
    }
}
