<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Keyword, AdminSetting, Label, keyword_label, Website_last_updated, Country};
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7\Request as GzRequest;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleAdsService;
use App\Services\KeywordDataUpdate;

class KeywordController extends Controller
{

    public function show()
    {
        // if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
        //     $keywords = Keyword::with('user')->where('website_id', auth()->user()->website_id)->get();
        // } else {
            $keywords = Keyword::where('user_id', auth()->id())->where('website_id', auth()->user()->website_id)->get();
        // }
        return view('keyword.list', compact('keywords'));
    }

    // public function dashboard(Request $request)
    // {
    //     $labelIds = $request->input('labels', []);
    //     $countries = Country::all();
    //     $selectedCountry = auth()->user()->country_id ?? 3;
    //     $labels = Label::all();

    //     $ranges = [
    //         '1-10' => 0,
    //         '11-20' => 0,
    //         '21-30' => 0,
    //         '31-40' => 0,
    //         '41-50' => 0
    //     ];

    //     $user = auth()->user();
    //     $isAdmin = $user->hasRole('Admin');
    //     $isSuperAdmin = $user->hasRole('Super Admin');
    //     $keywordsQuery = Keyword::with(['keywordData' => function($query) use ($selectedCountry) {
    //         $query->where('country_id', $selectedCountry);
    //     }]);

    //     if ($isAdmin || $isSuperAdmin) {
    //         $keywordsQuery->where('website_id', $user->website_id);
    //     } else {
    //         $keywordsQuery->forUserAndWebsite($user->id, $user->website_id);
    //     }

    //     if (!empty($labelIds)) {
    //         $keywordsQuery->filterByLabels($labelIds);
    //     }

    //     $keywords = $keywordsQuery->get();
    //     if ($keywords->isEmpty()) {
    //         return view('dashboard', compact('keywords', 'labels', 'labelIds', 'ranges', 'countries', 'selectedCountry'));
    //     }

    //     foreach ($keywords as $keyword) {
    //         foreach ($keyword->keywordData as $data) {
    //             if ($data->position >= 1 && $data->position <= 10) {
    //                 $ranges['1-10']++;
    //             } elseif ($data->position >= 11 && $data->position <= 20) {
    //                 $ranges['11-20']++;
    //             } elseif ($data->position >= 21 && $data->position <= 30) {
    //                 $ranges['21-30']++;
    //             } elseif ($data->position >= 31 && $data->position <= 40) {
    //                 $ranges['31-40']++;
    //             } elseif ($data->position >= 41 && $data->position <= 50) {
    //                 $ranges['41-50']++;
    //             }
    //         }
    //     }

    //     return view('dashboard', compact('keywords', 'ranges', 'labels', 'labelIds', 'countries', 'selectedCountry'));
    // }
    public function dashboard(Request $request)
    {
        $labelIds = $request->input('labels', []);
        $countries = Country::all();
        $selectedCountry = auth()->user()->country_id ?? 3;
        $labels = Label::all();

        $ranges = [
            '1-10' => 0,
            '11-20' => 0,
            '21-30' => 0,
            '31-40' => 0,
            '41-50' => 0
        ];

        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $isSuperAdmin = $user->hasRole('Super Admin');

        $keywordsQuery = Keyword::with(['keywordData' => function($query) use ($selectedCountry) {
            $query->where('country_id', $selectedCountry);
        }]);

        if ($isAdmin || $isSuperAdmin) {
            $keywordsQuery->where('website_id', $user->website_id);
        } else {
            $keywordsQuery->forUserAndWebsite($user->id, $user->website_id);
        }

        if (!empty($labelIds)) {
            $keywordsQuery->filterByLabels($labelIds);
        }

        $keywords = $keywordsQuery->get();

        foreach ($keywords as $keyword) {
            if ($keyword->keywordData->isEmpty()) {
                $keyword->keywordData = collect([ (object)[
                    'position' => 0,
                    'search_volume' => 0,
                    'clicks' => 0,
                    'impression' => 0,
                    'competition' => 0,
                    'bid_rate_low' => 0,
                    'bid_rate_high' => 0
                ]]);
            } else {
                foreach ($keyword->keywordData as $data) {
                    if ($data->position >= 1 && $data->position <= 10) {
                        $ranges['1-10']++;
                    } elseif ($data->position >= 11 && $data->position <= 20) {
                        $ranges['11-20']++;
                    } elseif ($data->position >= 21 && $data->position <= 30) {
                        $ranges['21-30']++;
                    } elseif ($data->position >= 31 && $data->position <= 40) {
                        $ranges['31-40']++;
                    } elseif ($data->position >= 41 && $data->position <= 50) {
                        $ranges['41-50']++;
                    }
                }
            }
        }

        return view('dashboard', compact('keywords', 'ranges', 'labels', 'labelIds', 'countries', 'selectedCountry'));
    }


    public function create()
    {
        $labels = Label::all();
        return view('keyword.create', compact('labels'));
    }

    public function edit(Keyword $keyword)
    {
        $labels = Label::all();
        return view('keyword.edit', compact('keyword', 'labels'));
    }

    public function update(Request $request, Keyword $keyword)
    {
        $request->validate([
            'keyword' => 'required|string',
            'labels' => 'array',
            'labels.*' => 'exists:labels,id'
        ]);
        $keyword->keyword = $request->keyword;
        $keyword->save();

        $keyword->labels()->sync($request->labels);
        session()->flash('message', 'Keywords updated successfully');
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
        $keyWordExplode = explode(',', $keywords[0]);
        $labels = $request->label;
        foreach ($keyWordExplode as $keywordValue) {
            $keyword = new Keyword();
            $keyword->user_id = auth()->id();
            $keyword->website_id = auth()->user()->website_id;
            $keyword->keyword = $keywordValue;
            $keyword->ip_address = $ipaddress;
            $keyword->save();
            if($labels){
                foreach ($labels as $label) {
                    $keyword_label = new keyword_label();
                    $keyword_label->keyword_id = $keyword->id;
                    $keyword_label->label_id = $label;
                    $keyword_label->save();
                }
            }
        }
        session()->flash('message', 'Keywords saved successfully');
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


    public function set_country(Request $request)
    {
        $request->validate([
            'country_id' => 'required'
        ]);
    
        $country = Country::where('id', $request->country_id)->first();
    
        if (!$country) {
            return json_encode(['error' => 'Country not found']);
        }
        $user = auth()->user();
        $user->country_id = $country->id;
        $user->save();
    
        return json_encode(['success' => 'Country changed successfully']);
    }
}
