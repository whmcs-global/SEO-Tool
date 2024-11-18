<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Website, Website_last_updated, User, CronStatus, User_project};
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
            'property_id' => 'required|integer',
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
            'property_id' => $validatedData['property_id'],
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
        $assignedProjects = User_project::where('user_id', $user->id)->pluck('website_id');
        if ($user->hasRole('Super Admin')) {
            $websites = Website::all();
        } else {
            $websites = Website::whereIn('id', $assignedProjects)->get();
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
            'property_id' => 'required|integer',
        ]);

        $website = Website::findOrFail($id);
        $website->update($validatedData);

        return redirect()->route('admin.projects')->with(['status' => 'success', 'message'=> 'Website updated successfully!']);
    }

    public function refresh_data()
    {
        $cmd = 'php artisan keywords:update-metrics';
        $basePath = base_path();
        $fullCmd = "cd $basePath && $cmd";

        exec($fullCmd, $output, $return_var);

        if ($return_var === 0) {
            return ['success' => 'Data updated successfully', 'output' => $output, 'code' => 200];
        } else {
            return ['error' => 'Failed to update data', 'output' => $output, 'code' => $return_var];
        }
    }


    public function checkCronStatus(Request $request)
    {
        $cron_status = true;
        $currentDir = base_path();
        $cronJobEntry = "* * * * * cd $currentDir && php artisan schedule:run >> /dev/null 2>&1";
        $os = PHP_OS_FAMILY;
        $crons = CronStatus::orderBy('id', 'desc')->get();
        if ($os === 'Windows') {
            $output = shell_exec('schtasks /query /fo LIST');
            if (strpos($output, $cronJobEntry) !== false) {
                $cron_status = true;
            }
        } elseif ($os === 'Darwin' || $os === 'Linux') {
            $output = shell_exec('crontab -l');
            if (strpos($output, $cronJobEntry) !== false) {
                $cron_status = true;
            }
        } else {
            return redirect()->back()->with(['status' => 'error', 'message'=> 'Unsupported OS']);
        }

        $lastCron = CronStatus::latest()->first();
        $lastRunTime = $lastCron ? $lastCron->created_at : null;
        $hoursAgo = $lastRunTime ? now()->diffForHumans($lastRunTime) : null;

        return view('website.cron_status', compact('cron_status', 'crons', 'lastCron', 'lastRunTime', 'hoursAgo'));
    }

    public function cronLogs($id)
    {
        $crons = CronStatus::where('id', $id)->with('externalApiLogs')->get();
        return view('website.cron_logs', compact('crons'));
    }

    public function retryCron($id)
    {
        $cron = CronStatus::findOrFail($id);
        $cron->update(['status' => 2]);

        if ($cron->cron_name === 'GSC Data Fetch') {
            $cmd = 'php artisan keywords:update-metrics';
        } elseif ($cron->cron_name === 'Fetch New Keyword') {
            $cmd = 'php artisan keyword:fetch-new-keyword';
        } else {
            return response()->json(['error' => 'Invalid cron job'], 400);
        }

        $basePath = base_path();
        if (PHP_OS_FAMILY === 'Windows') {
            $fullCmd = "cd /d $basePath && start /B $cmd";
        } else {
            $fullCmd = "cd $basePath && $cmd &";
        }

        exec($fullCmd, $output, $return_var);

        if ($return_var === 0) {
            return response()->json(['success' => 'Cron job retried successfully', 'output' => $output, 'code' => 200]);
        } else {
            return response()->json(['error' => 'Failed to retry cron job', 'output' => $output, 'code' => $return_var], 400);
        }
    }
}
