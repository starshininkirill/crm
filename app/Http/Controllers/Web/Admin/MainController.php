<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class MainController extends Controller
{
    public function admin(){
        
        return Inertia::render('Admin/Main/Main');
    }
}
 