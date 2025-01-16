<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PhpOffice\PhpWord\Writer\Word2007\Part\Rels;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        Auth::logout();

        return redirect()->route('home');
    }
}
