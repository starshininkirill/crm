<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index()
    {
        return Inertia::render('Lk/Index');
    }
}
