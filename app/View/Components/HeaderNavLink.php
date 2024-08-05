<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Route;

class HeaderNavLink extends Component
{
    public $href;
    public $route;

    public function __construct($href, $route)
    {
        $this->href = $href;
        $this->route = $route;
    }

    public function isActive()
    {
        return Route::is($this->route);
    }

    public function render()
    {
        return view('components.header-nav-link');
    }
}