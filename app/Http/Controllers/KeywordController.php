<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Keyword, AdminSetting, Label, keyword_label, Website_last_updated, Country, User, AssignKeyword, KeywordData};
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7\Request as GzRequest;
use App\Services\GoogleAnalyticsService;
use App\Services\GoogleAdsService;
use App\Services\KeywordDataUpdate;
use App\Traits\KeywordDaterange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
{
    use KeywordDaterange;

    public function keywords_detail(Request $request)
    {
        $labelIds = $request->input('labels', []);
        $userIds = $request->input('users', []);
        $labels = Label::all();
        $countries = Country::all();
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $isSuperAdmin = $user->hasRole('Super Admin');
        $selectedCountry = $request->get('country', $user->country_id ?? 3);
        $users = User::all();
        $startDate = Carbon::yesterday()->subDays(1)->format('Y-m-d');
        $endDate = Carbon::today()->subDays(1)->format('Y-m-d');

        if ($request->has('daterange') && !empty($request->get('daterange'))) {
            list($start, $end) = explode(' - ', $request->get('daterange'));
            $startDate = Carbon::parse($start)->format('Y-m-d');
            $endDate = Carbon::parse($end)->format('Y-m-d');
        }

        $positionFilter = $request->get('positionFilter', 'all');

        $keywordsQuery = Keyword::with(['keywordData']);
        $keywordsQuery->where('website_id', $user->website_id);
        if ($isAdmin || $isSuperAdmin && !empty($userIds)) {
            if (is_array($userIds) && !empty($userIds)) {
                $keywordsQuery->whereIn('user_id', $userIds);
            }
        }
        if (!empty($labelIds)) {
            $keywordsQuery->filterByLabels($labelIds);
        }

        $keywords = $keywordsQuery->get();
        $totalKeywords = $keywords->count();

        $assignedKeywords = AssignKeyword::where('user_id', $user->id)->with('keyword')->get();

        $allKeywords = $keywords->merge($assignedKeywords->pluck('keyword'));

        $countryRanges = [];
        foreach ($countries as $country) {
            $countryRanges[$country->id] = [
                'top_5' => [
                    'keyword_count' => 0,
                    'percentage' => 0,
                    'keywords_position_increase_count' => 0,
                    'keywords_position_decrease_count' => 0,
                    'start_count' => 0,
                    'end_count' => 0
                ],
                'top_10' => [
                    'keyword_count' => 0,
                    'percentage' => 0,
                    'keywords_position_increase_count' => 0,
                    'keywords_position_decrease_count' => 0,
                    'start_count' => 0,
                    'end_count' => 0
                ],
                'top_50' => [
                    'keyword_count' => 0,
                    'percentage' => 0,
                    'keywords_position_increase_count' => 0,
                    'keywords_position_decrease_count' => 0,
                    'start_count' => 0,
                    'end_count' => 0
                ],
                'top_100' => [
                    'keyword_count' => 0,
                    'percentage' => 0,
                    'keywords_position_increase_count' => 0,
                    'keywords_position_decrease_count' => 0,
                    'start_count' => 0,
                    'end_count' => 0
                ],
            ];
        }

        $allDates = [];
        $keywordData = [];

        foreach ($allKeywords as $keyword) {
            if (!$keyword->keywordData) {
                continue;
            }

            foreach ($keyword->keywordData as $data) {
                $response = json_decode($data->response, true);
                if (!is_array($response)) {
                    continue;
                }

                $positionDates = [];
                foreach ($response as $entry) {
                    if (isset($entry['keys'][1], $entry['position'])) {
                        $date = $entry['keys'][1];
                        if (($startDate && $date < $startDate) || ($endDate && $date > $endDate)) {
                            continue;
                        }
                        $positionDates[$date] = [
                            'position' => $entry['position'],
                            'clicks' => $entry['clicks'],
                            'impressions' => $entry['impressions']
                        ];
                        $allDates[] = $date;
                    }
                }
                // Calculate keyword movements and count them in each range

                $startPosition = isset($positionDates[$startDate]['position']) ? (int)$positionDates[$startDate]['position'] : null;
                $endPosition = isset($positionDates[$endDate]['position']) ? (int)$positionDates[$endDate]['position'] : null;

                foreach (['top_5' => 5, 'top_10' => 10, 'top_50' => 50, 'top_100' => 100] as $range => $limit) {
                    if ($startPosition !== null && $startPosition <= $limit) {
                        $countryRanges[$data->country_id][$range]['start_count']++;
                        // if ($range === 'top_5' && $data->country_id == 1) {
                        //     Log::info("Keyword ID {$data->keyword_id} is in top_5 for country 1 with start position $startPosition");
                        // }
                    }

                    if ($endPosition !== null && $endPosition <= $limit) {
                        $countryRanges[$data->country_id][$range]['end_count']++;
                        // if ($range === 'top_5' && $data->country_id == 1) {
                        //     Log::info("Keyword ID {$data->keyword_id} is in top_5 for country 1 with end position $endPosition");
                        // }

                        if ($startPosition !== null && $endPosition !== null) {
                            if ($startPosition > $endPosition) {
                                $countryRanges[$data->country_id][$range]['keywords_position_increase_count']++;
                            } elseif ($startPosition < $endPosition) {
                                $countryRanges[$data->country_id][$range]['keywords_position_decrease_count']++;
                            }
                        }
                    }
                }

                if ($data->country_id == $selectedCountry) {
                    if ($positionFilter == 'all' || $this->isPositionInFilter($positionDates, $positionFilter)) {
                        $keywordData[] = [
                            'keyword' => $keyword->keyword,
                            'keyword_label' => $keyword->labels->pluck('name')->toArray(),
                            'country' => $data->country->name ?? 'Unknown',
                            'country_id' => $data->country_id,
                            'search_volume' => $data->search_volume,
                            'impression' => $data->impression,
                            'competition' => $data->competition,
                            'positions' => $positionDates,
                        ];
                    }
                }
            }
        }
        if ($positionFilter != 'all') {
            $filteredKeywordData = [];
            foreach ($keywordData as $data) {
                if (isset($data['positions'][$endDate])) {
                    $position = (int) $data['positions'][$endDate]['position'];
                    if (($positionFilter == 'top_5' && $position <= 5) ||
                        ($positionFilter == 'top_10' && $position <= 10) ||
                        ($positionFilter == 'top_50' && $position <= 50) ||
                        ($positionFilter == 'top_100' && $position <= 100)
                    ) {
                        $filteredKeywordData[] = $data;
                    }
                }
            }
            $keywordData = $filteredKeywordData;
        }
        $allDates = array_unique($allDates);
        sort($allDates);
        return view('keyword.details', compact('users', 'userIds', 'keywordData', 'countryRanges', 'countries', 'totalKeywords', 'startDate', 'endDate', 'allDates', 'selectedCountry', 'positionFilter', 'labels', 'labelIds'));
    }

    private function isPositionInFilter($positionDates, $positionFilter)
    {
        foreach ($positionDates as $date => $data) {
            $position = (int)$data['position'] ?? null;
            if (($positionFilter == 'top_5' && $position <= 5) ||
                ($positionFilter == 'top_10' && $position <= 10) ||
                ($positionFilter == 'top_50' && $position <= 50) ||
                ($positionFilter == 'top_100' && $position <= 100)
            ) {
                return true;
            }
        }
        return false;
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

    public function dashboard(Request $request)
    {
        $userId = auth()->id();

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

        // Fetch keywords
        $keywordsQuery = Keyword::with(['keywordData' => function ($query) use ($selectedCountry) {
            $query->where('country_id', $selectedCountry);
        }]);

        $keywordsQuery->where('website_id', $user->website_id);
        // Check for the keyword type filter
        $keywordType = $request->input('keyword-type', 'all');
        // dd($keywordType);
        if ($keywordType == 'only-me') {
            // dd('only-me');
            $keywordsQuery->where('user_id', $user->id);
        }
        // dd($keywordsQuery->get());

        if (!empty($labelIds)) {
            $keywordsQuery->filterByLabels($labelIds);
        }

        $keywords = $keywordsQuery->get();

        // Fetch assigned keywords using AssignKeyword
        $assignedKeywords = AssignKeyword::where('user_id', $userId)->with(['keyword' => function ($query) {
            $query->with(['keywordData' => function ($query) {
                $query->where('country_id', auth()->user()->country_id);
            }])->where('website_id', auth()->user()->website_id);
        }])->get();
        $assignedKeywordPluck = $assignedKeywords->pluck('keyword');
        if (null === $assignedKeywordPluck) {
            $allKeywords = $keywords;
        } else {
            $allKeywords = $keywords->merge($assignedKeywordPluck);
        }
        // Process keyword data
        foreach ($allKeywords as $keyword) {
            if ($keyword->keywordData->isEmpty()) {
                $keyword->keywordData = collect([(object)[
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
        // dd($allKeywords);
        return view('dashboard', compact('allKeywords', 'ranges', 'labels', 'labelIds', 'countries', 'selectedCountry'));
    }

    public function create()
    {
        $users = [];
        if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
            $users = User::all();
        }
        $labels = Label::all();
        return view('keyword.create', compact('labels', 'users'));
    }

    public function edit(Keyword $keyword)
    {
        $users = [];
        if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Super Admin')) {
            $users = User::all();
        }
        $labels = Label::all();
        return view('keyword.edit', compact('keyword', 'labels', 'users'));
    }

    public function update(Request $request, Keyword $keyword)
    {
        $request->validate([
            'keyword' => 'required|string',
            'labels' => 'array',
            'labels.*' => 'exists:labels,id',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $keyword->keyword = $request->keyword;
        $keyword->save();

        $keyword->labels()->sync($request->labels);
        $keyword->assignedUsers()->sync($request->users);

        session()->flash('message', 'Keyword updated successfully');
        return redirect()->route('dashboard');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keywords' => 'required|string',
        ], [
            'keywords.required' => 'Please enter at least one keyword',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            session()->flash('message', $errors);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $ipaddress = $this->getUserIP();
        $keywordsString = $request->keywords;
        $keywordsArray = explode(',', $keywordsString);
        $labels = $request->label;
        $userId = auth()->id();
        $websiteId = auth()->user()->website_id;
        $userIds = $request->users; // Array of user IDs selected for assignment

        foreach ($keywordsArray as $keywordValue) {
            $keywordValue = trim($keywordValue);

            // Check if the keyword already exists for the user and website
            $existingKeyword = Keyword::where('user_id', $userId)
                ->where('website_id', $websiteId)
                ->where('keyword', $keywordValue)
                ->first();

            if ($existingKeyword) {
                session()->flash('message', 'The keyword "' . $keywordValue . '" has already been added.');
                return redirect()->back();
            }

            // Create a new keyword
            $keyword = new Keyword();
            $keyword->user_id = $userId;
            $keyword->website_id = $websiteId;
            $keyword->keyword = $keywordValue;
            $keyword->ip_address = $ipaddress;
            $keyword->save();

            // Attach labels to the keyword if provided
            if ($labels) {
                $keyword->labels()->sync($labels);
            }

            // Assign the keyword to selected users
            $keyword->assignedUsers()->sync($userIds);
        }

        session()->flash('message', 'Keywords saved successfully');
        return redirect()->route('keywords.details');
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

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
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

    public function keyword_data()
    {
        $keywords = Keyword::with(['keywordData' => function ($query) {
            $query->with('country');
        }])->get();

        foreach ($keywords as $keyword) {
            foreach ($keyword->keywordData as $data) {
                $response = $this->keywordbydate($keyword, $data->country->ISO_CODE);
                $data['response'] = $response;
                $data->save();
            }
        }
    }

    // public function new_dashboard(Request $request)
    // {
    //     $yesterday = Carbon::yesterday()->subDays(2)->format('Y-m-d');
    //     $today = Carbon::today()->subDays(2)->format('Y-m-d');
    //     // dd($yesterday, $today, auth()->user()->country_id);
    //     $downKeywords = [];
    //     $upKeywords = [];

    //     $newKeywords = Keyword::select('keywords.*', 'keyword_data.*')
    //         ->join('keyword_data', function($join) {
    //             $join->on('keywords.id', '=', 'keyword_data.keyword_id')
    //                  ->where('keyword_data.country_id', auth()->user()->country_id);
    //         })
    //         ->whereNull('keywords.user_id')
    //         ->where('keywords.website_id', auth()->user()->website_id)
    //         ->doesntHave('assignedUsers')
    //         ->get();

    //     $Keywords = Keyword::select('keywords.*', 'keyword_data.*')
    //         ->join('keyword_data', function($join) {
    //             $join->on('keywords.id', '=', 'keyword_data.keyword_id')
    //                  ->where('keyword_data.country_id', auth()->user()->country_id);
    //         })
    //         ->where('keywords.website_id', auth()->user()->website_id)
    //         ->get();

    //     foreach ($Keywords as $keyword) {
    //         $response = $keyword->response;
    //         $response = json_decode($response, true);

    //         if (is_array($response)) {
    //             usort($response, function($a, $b) {
    //                 return strtotime($a['keys'][1]) - strtotime($b['keys'][1]);
    //             });

    //             $previousDatePosition = null;
    //             $currentDatePosition = null;

    //             foreach ($response as $data) {
    //                 $currentPosition = $data['position'];
    //                 $date = $data['keys'][1];

    //                 if ($date == $yesterday) {
    //                     $previousDatePosition = $currentPosition;
    //                 }

    //                 if ($date == $today) {
    //                     $currentDatePosition = $currentPosition;
    //                 }
    //             }

    //             if ($previousDatePosition !== null && $currentDatePosition !== null) {
    //                 if ($currentDatePosition > $previousDatePosition) {
    //                     $downKeywords[] = [
    //                         'keyword' => $keyword,
    //                         'previous_position' => $previousDatePosition,
    //                         'current_position' => $currentDatePosition,
    //                     ];
    //                 } elseif ($currentDatePosition < $previousDatePosition) {
    //                     $upKeywords[] = [
    //                         'keyword' => $keyword,
    //                         'previous_position' => $previousDatePosition,
    //                         'current_position' => $currentDatePosition,
    //                     ];
    //                 }
    //             }
    //         }
    //     }
    //     return view('new_dashboard', compact('newKeywords', 'downKeywords', 'upKeywords', 'today', 'yesterday'));
    // }

    public function new_dashboard(Request $request)
    {
        $yesterday = Carbon::yesterday()->subDays(2)->format('Y-m-d');
        $today = Carbon::today()->subDays(2)->format('Y-m-d');
        $downKeywords = [];
        $upKeywords = [];
        $sameKeywords = [];
        $totalPreviousPositions = 0;
        $totalCurrentPositions = 0;
        $keywordCount = 0;


        $newKeywords = Keyword::select('keywords.*', 'keyword_data.*')
            ->join('keyword_data', function ($join) {
                $join->on('keywords.id', '=', 'keyword_data.keyword_id')
                    ->where('keyword_data.country_id', auth()->user()->country_id);
            })
            ->whereNull('keywords.user_id')
            ->where('keywords.website_id', auth()->user()->website_id)
            ->doesntHave('assignedUsers')
            ->get();

        $Keywords = Keyword::select('keywords.*', 'keyword_data.*')
            ->join('keyword_data', function ($join) {
                $join->on('keywords.id', '=', 'keyword_data.keyword_id')
                    ->where('keyword_data.country_id', auth()->user()->country_id);
            })
            ->where('keywords.website_id', auth()->user()->website_id)
            ->get();

        $totalKeywords = count($Keywords);
        $totalPositionChange = 0;
        $topImproved = null;
        $topDeclined = null;
        foreach ($Keywords as $keyword) {
            $response = $keyword->response;
            $response = json_decode($response, true);

            if (is_array($response)) {
                usort($response, function ($a, $b) {
                    return strtotime($a['keys'][1]) - strtotime($b['keys'][1]);
                });

                $previousDatePosition = null;
                $currentDatePosition = null;

                foreach ($response as $data) {
                    $currentPosition = $data['position'];
                    $date = $data['keys'][1];

                    if ($date == $yesterday) {
                        $previousDatePosition = $currentPosition;
                    }

                    if ($date == $today) {
                        $currentDatePosition = $currentPosition;
                    }
                }

                if ($previousDatePosition !== null && $currentDatePosition !== null) {
                    $keywordCount++;
                    $totalPreviousPositions += $previousDatePosition;
                    $totalCurrentPositions += $currentDatePosition;

                    if ($currentDatePosition > $previousDatePosition) {
                        $downKeywords[] = [
                            'keyword' => $keyword,
                            'previous_position' => $previousDatePosition,
                            'current_position' => $currentDatePosition,
                        ];
                    } elseif ($currentDatePosition < $previousDatePosition) {
                        $upKeywords[] = [
                            'keyword' => $keyword,
                            'previous_position' => $previousDatePosition,
                            'current_position' => $currentDatePosition,
                        ];
                    } else {
                        $sameKeywords[] = [
                            'keyword' => $keyword,
                            'previous_position' => $previousDatePosition,
                            'current_position' => $currentDatePosition,
                        ];
                    }

                    if ($previousDatePosition !== null && $currentDatePosition !== null) {
                        $positionChange = $previousDatePosition - $currentDatePosition;
                        $totalPositionChange += $positionChange;

                        if ($positionChange > 0 && (!$topImproved || $positionChange > $topImproved['change'])) {
                            $topImproved = [
                                'keyword' => $keyword->keyword,
                                'change' => $positionChange
                            ];
                        } elseif ($positionChange < 0 && (!$topDeclined || $positionChange < $topDeclined['change'])) {
                            $topDeclined = [
                                'keyword' => $keyword->keyword,
                                'change' => $positionChange
                            ];
                        }
                    }
                }
            }
        }

        $keywordStats = [
            'up' => count($upKeywords),
            'down' => count($downKeywords),
            'same' => count($sameKeywords),
            'avgPreviousPosition' => $keywordCount > 0 ? round($totalPreviousPositions / $keywordCount, 2) : 0,
            'avgCurrentPosition' => $keywordCount > 0 ? round($totalCurrentPositions / $keywordCount, 2) : 0,
            'total' => $totalKeywords,
            'avgPositionChange' => $totalKeywords > 0 ? round($totalPositionChange / $totalKeywords, 2) : 0,
            'topImproved' => $topImproved,
            'topDeclined' => $topDeclined
        ];

        return view('new_dashboard', compact('newKeywords', 'downKeywords', 'upKeywords', 'today', 'yesterday', 'keywordStats'));
    }
}
