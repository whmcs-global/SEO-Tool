<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AlertComponent extends Component
{
    public $message;
    public $status;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->status = session('status');
        $this->message = session('message');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert-component');
    }
}
