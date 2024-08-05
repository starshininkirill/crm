<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home(){
        return view('base');   
    }
    public function admin(){
        return view('admin');   
    }
}
