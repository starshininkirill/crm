<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\WorkPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MainController extends Controller
{
    public function home()
    {
        return Inertia::render('Main/Main'); 
    }

    public function loginHome(Request $request)
    {
        Auth::attempt([
            'email' => 'admin@mail.ru',
            'password' => '1409199696Rust'
        ]);

        $request->session()->regenerate();

        return redirect()->route('home');   
    }
    public function admin(){
        
        return Inertia::render('Admin/Main/Main');
    }
}
 