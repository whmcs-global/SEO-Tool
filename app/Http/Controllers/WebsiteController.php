<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;

class WebsiteController extends Controller
{
    public function create()
    {
        return view('website.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'GOOGLE_ANALYTICS_CLIENT_ID' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_CLIENT_SECRET' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_REDIRECT_URI' => 'required|url|max:255',
            'API_KEY' => 'required|string|max:255',
        ]);
    
        $website = Website::create([
            'name' => $validatedData['name'],
            'user_id' => auth()->user()->id,
            'url' => $validatedData['url'],
            'GOOGLE_ANALYTICS_CLIENT_ID' => $validatedData['GOOGLE_ANALYTICS_CLIENT_ID'],
            'GOOGLE_ANALYTICS_CLIENT_SECRET' => $validatedData['GOOGLE_ANALYTICS_CLIENT_SECRET'],
            'GOOGLE_ANALYTICS_REDIRECT_URI' => $validatedData['GOOGLE_ANALYTICS_REDIRECT_URI'],
            'API_KEY' => $validatedData['API_KEY'],
        ]);
    
        return redirect()->route('home')->with('success', 'Website added successfully!');
    }

    public function set_website(Website $website)
    {   $user = auth()->user();
        $user->website_id = $website->id;
        $user->save();
        return redirect()->back();
    }

    public function set_website_default(Website $website)
    {   $user = auth()->user();
        $user->website_id = null;
        $user->save();
        return redirect()->back();
    }

    public function projects(Request $request)
    {
        $websites = Website::all();
        // dd('working on it');
        return view('website.projects', compact('websites'));
    }
}
