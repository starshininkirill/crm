<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends Controller
{
    public function index(){

        $categories = ServiceCategory::all();

        return view('admin.service.category.index', ['categories' => $categories]);
    }

    public function create(){
        return view('admin.service.category.create');
    }

    public function store(ServiceCategoryRequest $request){

        $validated = $request->validated();

        ServiceCategory::create($validated);

        return redirect()->back()->with('success', 'Категория успешно создана');
    }
}
