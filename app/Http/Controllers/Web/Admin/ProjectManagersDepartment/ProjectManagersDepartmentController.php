<?php

namespace App\Http\Controllers\Web\Admin\ProjectManagersDepartment;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ProjectManagersDepartmentController extends Controller
{
    public function index()
    {

        return Inertia::render('Admin/ProjectsDepartment/Index', []);
    }

    public function report()
    {

        return Inertia::render('Admin/ProjectsDepartment/Report', []);
    }
}
