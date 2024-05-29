<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use App\Models\Website;

class WebsiteViewComposer
{
    public function compose(View $view)
    {
        $websites = Website::all();
        $view->with('websites', $websites);
    }
}