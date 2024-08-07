<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class AdminSubnavLink extends Component
{
    public $href;
    public $route;

    public function __construct($route)
    {
        $this->route = $route;
        $this->href = route($route);
    }

    public function isActive()
    {
        return Route::is($this->route);
    }

    public function render()
    {
        return view('components.admin-subnav-link');
    }
}
 