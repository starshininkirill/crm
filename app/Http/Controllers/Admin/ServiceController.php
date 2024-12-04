<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceStoreRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index($serviceCategoryId = null){

        if ($serviceCategoryId) {
            $services = Service::where('service_category_id', $serviceCategoryId)->get();
        } else {
            $services = Service::all();
        };

        return view('admin.service.index', ['services' => $services->sortBy('name')]);
    }

    public function create(){
        $categories = ServiceCategory::all();

        return view('admin.service.create', ['categories' => $categories]);
    }

    public function edit(Service $service){
        
        $categories = ServiceCategory::all();

        return view('admin.service.edit', [
            'categories' => $categories,
            'service' => $service
        ]);
    }


}
