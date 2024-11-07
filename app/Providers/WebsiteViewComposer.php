<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use App\Models\{Website, User_project, Website_last_updated, Country};
use Illuminate\Support\Facades\Auth;

class WebsiteViewComposer
{
    public function compose(View $view)
    {
        $userId = Auth::id();
        $selected_project_ids = User_project::where('user_id', $userId)->pluck('website_id')->toArray();
        $lastUpdated = Website_last_updated::where('website_id', Auth::user()->website_id)->value('last_updated_at');

        $websites = Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin')
            ? Website::all()
            : Website::whereIn('id', $selected_project_ids)->get();

        $countries = Country::all();

        $selectedCountry = Auth::user()->country_id ?? 3;

        $view->with([
            'websites' => $websites,
            'lastUpdated' => $lastUpdated,
            'countries' => $countries,
            'selectedCountry' => $selectedCountry,
        ]);
    }
}
