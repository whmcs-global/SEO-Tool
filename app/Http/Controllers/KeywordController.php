<?php

namespace App\Http\Controllers;

use App\Traits\{KeywordAnalytic};
use Illuminate\Http\Request;
use App\Models\{Keyword, AdminSetting};
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7\Request as GzRequest;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleAdsService;

class KeywordController extends Controller
{

    use KeywordAnalytic;

    protected $googleAnalyticsService;

    public function __construct(GoogleAnalyticsService $googleAnalyticsService)
    {
        $this->googleAnalyticsService = $googleAnalyticsService;
        // $this->googleAdsService = $googleAdsService;
    }

    public function show()
    {
        // if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
        //     $keywords = Keyword::with('user')->where('website_id', auth()->user()->website_id)->get();
        // } else {
            $keywords = Keyword::where('user_id', auth()->id())->where('website_id', auth()->user()->website_id)->get();
        // }
        return view('keyword.list', compact('keywords'));
    }

    public function dashboard()
    {

        // $keywords = ['hostingseekers'];
        // $results = $this->googleAnalyticsService->getPageVisitsForKeyword($keyword);
        // $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords);
        // dd($metrics);
        // dd($results);
        // return response()->json($results);
        // if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
        //     $keywords = Keyword::with('user')->where('website_id', auth()->user()->website_id)->get();
        // } else {
            $keywords = Keyword::where('user_id', auth()->id())->where('website_id', auth()->user()->website_id)->get();
        // }
        $settings = AdminSetting::where('website_id', auth()->user()->website_id)->where('type','google')->first();
        $ranges = [
            '1-10' => 0,
            '11-20' => 0,
            '21-30' => 0,
            '31-40' => 0,
            '41-50' => 0
        ];
        if($settings){
            foreach ($keywords as $keyword) {
                $key = $this->keywords(request(), $keyword);
                if(isset($key['code'])){
                    $error_message = $key['message'];
                    // dd($error_message);
                    continue;
                }
                if($key){
                    $keyword->position = (int) $key[0]->position;
                    $keyword->clicks = (int) $key[0]->clicks;
                    $keyword->impressions = $key[0]->impressions;
                }
                else{
                    $keyword->position = '-';
                    $keyword->clicks = '-';
                    $keyword->impressions = '-';
                }
            }
            foreach ($keywords as $keyword) {
                if ($keyword->position >= 1 && $keyword->position <= 10) {
                    $ranges['1-10']++;
                } elseif ($keyword->position >= 11 && $keyword->position <= 20) {
                    $ranges['11-20']++;
                } elseif ($keyword->position >= 21 && $keyword->position <= 30) {
                    $ranges['21-30']++;
                } elseif ($keyword->position >= 31 && $keyword->position <= 40) {
                    $ranges['31-40']++;
                } elseif ($keyword->position >= 41 && $keyword->position <= 50) {
                    $ranges['41-50']++;
                }
            }
        }

        // dd($keywords);
        return view('dashboard', compact('keywords', 'ranges'));
    }

    public function create()
    {
        return view('keyword.create');
    }

    public function edit(Keyword $keyword)
    {
        return view('keyword.edit', compact('keyword'));
    }

    public function update(Request $request, Keyword $keyword)
    {
        $request->validate([
            'keyword' => 'required|string',
        ]);
        $keyword->keyword = $request->keyword;
        $keyword->save();
        return redirect()->route('dashboard');
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keywords' => 'required|array',
            'keywords.*' => 'required|string|unique:keywords,keyword,NULL,id,user_id,' . auth()->id() . ',website_id,' . auth()->user()->website_id,
        ], [
            'keywords.required' => 'Please enter at least one keyword',
            'keywords.*.required' => 'Please enter a keyword',
            'keywords.*.unique' => 'The keyword has already been added.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $ipaddress=$this->getUserIP();
        $keywords = $request->keywords;

        foreach ($keywords as $keywordValue) {
            $keyword = new Keyword();
            $keyword->user_id = auth()->id();
            $keyword->website_id = auth()->user()->website_id;
            $keyword->keyword = $keywordValue;
            $keyword->ip_address = $ipaddress;
            $keyword->save();
        }

        return response()->json(['success' => 'Keywords saved successfully'], 200);
    }

    public function destroy(Keyword $keyword)
    {
        $keyword->delete();
        return redirect()->back();
    }

    // get user ip
    function getUserIP()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
                $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
                $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }
        else{
            $ip = $remote;
        }
        return $ip;
    }
}
