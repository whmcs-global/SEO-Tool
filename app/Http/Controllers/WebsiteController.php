<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Website, Website_last_updated, User};
use App\Services\KeywordDataUpdate;

class WebsiteController extends Controller
{
    protected $keywordDataUpdate;

    public function create()
    {
        $user = auth()->user();
        // dd($user->projectconfig_status());
        return view('website.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'property_type'=> 'required|in:domain,url_prefix',
            'url' => 'required|max:255',
            'GOOGLE_ANALYTICS_CLIENT_ID' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_CLIENT_SECRET' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_REDIRECT_URI' => 'required|url|max:255',
            'API_KEY' => 'required|string|max:255',
            'GOOGLE_ADS_DEVELOPER_TOKEN' => 'required|string|max:255',
            'GOOGLE_ADS_CLIENT_ID' => 'required|string|max:255',
            'GOOGLE_ADS_CLIENT_SECRET' => 'required|string|max:255',
            'GOOGLE_ADS_REDIRECT_URI' => 'required|url|max:255',
            'GOOGLE_ADS_KEY' => 'required|string|max:255',
            'GOOGLE_ADS_LOGIN_CUSTOMER_ID' => 'required|string|max:255',
        ]);

        $website = Website::create([
            'name' => $validatedData['name'],
            'user_id' => auth()->user()->id,
            'property_type' => $validatedData['property_type'],
            'url' => $validatedData['url'],
            'GOOGLE_ANALYTICS_CLIENT_ID' => $validatedData['GOOGLE_ANALYTICS_CLIENT_ID'],
            'GOOGLE_ANALYTICS_CLIENT_SECRET' => $validatedData['GOOGLE_ANALYTICS_CLIENT_SECRET'],
            'GOOGLE_ANALYTICS_REDIRECT_URI' => $validatedData['GOOGLE_ANALYTICS_REDIRECT_URI'],
            'API_KEY' => $validatedData['API_KEY'],
            'GOOGLE_ADS_DEVELOPER_TOKEN' => $validatedData['GOOGLE_ADS_DEVELOPER_TOKEN'],
            'GOOGLE_ADS_CLIENT_ID' => $validatedData['GOOGLE_ADS_CLIENT_ID'],
            'GOOGLE_ADS_CLIENT_SECRET' => $validatedData['GOOGLE_ADS_CLIENT_SECRET'],
            'GOOGLE_ADS_REDIRECT_URI' => $validatedData['GOOGLE_ADS_REDIRECT_URI'],
            'GOOGLE_ADS_KEY' => $validatedData['GOOGLE_ADS_KEY'],
            'GOOGLE_ADS_LOGIN_CUSTOMER_ID' => $validatedData['GOOGLE_ADS_LOGIN_CUSTOMER_ID'],
        ]);

        $user = auth()->user();
        $user->website_id = $website->id;
        $user->save();
        return redirect()->route('admin.settings')->with('success', 'Website added successfully!');
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
        $user = auth()->user();
        if ($user->role === 'Super Admin') {
            $websites = Website::all();
        } else {
            $websites = Website::where('user_id', $user->id)->get();
        }
        return view('website.projects', compact('websites'));
    }

    public function delete(Website $website)
    {
        $all_users = User::where('website_id', $website->id)->get();
        foreach ($all_users as $user) {
            $user->website_id = $user->User_project()->first()->id ?? null;
            $user->save();
        }
        $website->delete();

        return redirect()->back()->with(['status' => 'success', 'message'=> 'Website deleted successfully!']);
    }

    public function edit($id)
    {
        $website = Website::findOrFail($id);
        return view('website.edit', compact('website'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'property_type'=> 'required|in:domain,url_prefix',
            'url' => 'required|max:255',
            'GOOGLE_ANALYTICS_CLIENT_ID' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_CLIENT_SECRET' => 'required|string|max:255',
            'GOOGLE_ANALYTICS_REDIRECT_URI' => 'required|url|max:255',
            'API_KEY' => 'required|string|max:255',
            'GOOGLE_ADS_DEVELOPER_TOKEN' => 'required|string|max:255',
            'GOOGLE_ADS_CLIENT_ID' => 'required|string|max:255',
            'GOOGLE_ADS_CLIENT_SECRET' => 'required|string|max:255',
            'GOOGLE_ADS_REDIRECT_URI' => 'required|url|max:255',
            'GOOGLE_ADS_KEY' => 'required|string|max:255',
            'GOOGLE_ADS_LOGIN_CUSTOMER_ID' => 'required|string|max:255',
        ]);

        $website = Website::findOrFail($id);
        $website->update($validatedData);

        return redirect()->route('admin.projects')->with(['status' => 'success', 'message'=> 'Website updated successfully!']);
    }

    public function refresh_data(KeywordDataUpdate $keywordDataUpdate)
    {
        $this->keywordDataUpdate = $keywordDataUpdate;
        Website_last_updated::updateOrCreate(['website_id' => auth()->user()->website_id], ['last_updated_at' => now()]);
        $this->keywordDataUpdate->update();
        return ['success' => 'Data updated successfully','code' => 200];
    }
}
