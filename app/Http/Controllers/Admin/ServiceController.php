<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){

        $services = Service::all();

        return view('admin.service.index', ['services' => $services]);
    }

    public function create(){
        return view('admin.service.create');
    }

    public function store(ServiceRequest $request){

        $validated = $request->validated();

        Service::create($validated);

        return redirect()->back()->with('success', 'Услуга успешно создана');
    }
}
