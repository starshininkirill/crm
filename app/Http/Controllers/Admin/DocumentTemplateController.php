<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentTemplateController extends Controller
{
    public function index()
    {
        $documetTemplates = DocumentTemplate::all();
        return Inertia::render('Admin/Organization/DocumentTemplate/Index',[
            'documetTemplates' => $documetTemplates,
        ]);
    }
}
