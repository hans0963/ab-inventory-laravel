<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AlertDelete extends Component
{
    public $route;
    public $message;

    public function __construct($route, $message = 'Are you sure?')
    {
        $this->route = $route;
        $this->message = $message;
    }

    public function render()
    {
        return view('components.alert-delete');
    }
}

