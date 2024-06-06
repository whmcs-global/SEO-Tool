<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use App\Models\{Website, User_project};
use Illuminate\Support\Facades\Auth;

class WebsiteViewComposer
{
    public function compose(View $view)
    {
        $userId = Auth::id();
        $selected_project_ids = User_project::where('user_id', $userId)->pluck('website_id')->toArray();
        if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin')){
            $websites = Website::all();
        }
        else{
            $websites = Website::whereIn('id', $selected_project_ids)->get();
        }
        $view->with('websites', $websites);
    }
}