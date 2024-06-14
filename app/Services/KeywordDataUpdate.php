<?php

// namespace App\Services;

// use App\Services\GoogleAdsService;
// use Illuminate\Http\Request;
// use App\Traits\{KeywordAnalytic};
// use App\Models\{Keyword, AdminSetting, Label, keyword_label};
// use Illuminate\Support\Facades\Validator;
// use GuzzleHttp\Psr7\Request as GzRequest;
// use Illuminate\Support\Facades\Log;


// class KeywordDataUpdate{

//     use KeywordAnalytic;
//     protected $googleAdsService;

//     public function __construct(GoogleAdsService $googleAdsService)
//     {
//         $this->googleAdsService = $googleAdsService;
//     }

//     public function update()
//     {
//         $keywords = Keyword::all();
//         if($keywords->isEmpty()){
//             return;
//         }
//         $keywords_ads = $keywords->pluck('keyword')->toArray();
//         $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords_ads);

//         foreach ($keywords as $keyword) {
//             foreach ($metrics as $metric) {
//                 if ($metric['text'] === $keyword->keyword) {
//                     $keyword->search_volume = $metric['keywordMetrics']['avgMonthlySearches'];
//                     $keyword->competition = $metric['keywordMetrics']['competition'];
//                     $keyword->bid_rate_low = $metric['keywordMetrics']['lowTopOfPageBidRupees'];
//                     $keyword->bid_rate_high = $metric['keywordMetrics']['highTopOfPageBidRupees'];
//                     $keyword->save();
//                     break;
//                 }
//             }
//         }
//             foreach ($keywords as $keyword) {
//                 $settings = AdminSetting::where('website_id', $keyword->website_id)->where('type', 'google')->first();
//                 $key = $this->keywords(request(), $keyword);
//                 if (isset($key['code'])) {
//                     $error_message = $key['message'];
//                     Log::error($error_message);
//                     continue;
//                 }
//                 if ($key) {
//                     $keyword->position = (int) $key[0]->position;
//                     $keyword->clicks = (int) $key[0]->clicks;
//                     $keyword->impression = $key[0]->impressions;
//                 } else {
//                     $keyword->position = 0;
//                     $keyword->clicks = 0;
//                     $keyword->impression = 0;
//                 }
//                 $keyword->save();
//             }
//     }
// }

namespace App\Services;

use App\Services\GoogleAdsService;
use Illuminate\Http\Request;
use App\Traits\KeywordAnalytic;
use App\Models\{Keyword, AdminSetting, Label, keyword_label};
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7\Request as GzRequest;
use Illuminate\Support\Facades\Log;

class KeywordDataUpdate
{
    use KeywordAnalytic;
    
    protected $googleAdsService;

    public function __construct(GoogleAdsService $googleAdsService)
    {
        $this->googleAdsService = $googleAdsService;
    }

    public function update()
    {
        $keywords = Keyword::all();

        if ($keywords->isEmpty()) {
            return;
        }

        $keywords_ads = $keywords->pluck('keyword')->toArray();
        $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords_ads);

        $metricsMap = [];
        foreach ($metrics as $metric) {
            $metricsMap[$metric['text']] = $metric['keywordMetrics'];
        }
        foreach ($keywords as $keyword) {
            if (isset($metricsMap[$keyword->keyword])) {
                $metric = $metricsMap[$keyword->keyword];
                $keyword->search_volume = $metric['avgMonthlySearches'];
                $keyword->competition = $metric['competition'];
                $keyword->bid_rate_low = $metric['lowTopOfPageBidRupees'];
                $keyword->bid_rate_high = $metric['highTopOfPageBidRupees'];
            }

            $settings = AdminSetting::where('website_id', $keyword->website_id)->where('type', 'google')->get();
            if ($settings) {
                $key = $this->keywords(request(), $keyword);
                if (isset($key['code'])) {
                    $error_message = $key['message'];
                    Log::error($error_message);
                    continue;
                }
                if ($key) {
                    $keyword->position = (int) $key[0]->position;
                    $keyword->clicks = (int) $key[0]->clicks;
                    $keyword->impression = $key[0]->impressions;
                } else {
                    $keyword->position = 0;
                    $keyword->clicks = 0;
                    $keyword->impression = 0;
                }
            }
            $keyword->save();
        }
    }
}
