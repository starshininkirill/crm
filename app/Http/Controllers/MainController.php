<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function home()
    {
        
        return view('base');   
    }
    public function loginHome()
    {
        Auth::attempt([
            'email' => 'admin@mail.ru',
            'password' => '1409199696Rust'
        ]);
        
        return view('base');   
    }
    public function admin(){
        return view('admin');   
    }
}
 