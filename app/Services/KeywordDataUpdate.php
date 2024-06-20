<?php
namespace App\Services;

use App\Services\GoogleAdsService;
use Illuminate\Http\Request;
use App\Traits\KeywordAnalytic;
use App\Models\{Keyword, AdminSetting, Label, keyword_label, Country, KeywordData};
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
        $countries = Country::all();
        foreach ($countries as $country) {
            $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords_ads, $country->Google_Code);
            $metricsMap = [];
            foreach ($metrics as $metric) {
                $metricsMap[$metric['text']] = $metric['keywordMetrics'];
            }
            foreach ($keywords as $keyword) {
                $keywordDataAttributes = [
                    'keyword_id' => $keyword->id,
                    'country_id' => $country->id,
                ];
    
                $keywordDataValues = [];
                if (isset($metricsMap[$keyword->keyword])) {
                    $metric = $metricsMap[$keyword->keyword];
                    $keywordDataValues = [
                        'search_volume' => $metric['avgMonthlySearches'],
                        'competition' => $metric['competition'],
                        'bid_rate_low' => $metric['lowTopOfPageBidRupees'],
                        'bid_rate_high' => $metric['highTopOfPageBidRupees'],
                    ];
                }
    
                $settings = AdminSetting::where('website_id', $keyword->website_id)
                                        ->where('type', 'google')
                                        ->get();
                if ($settings) {
                    $key = $this->keywords(request(), $keyword, $country->code);
                    if (isset($key['code'])) {
                        $error_message = $key['message'];
                        Log::error($error_message);
                        continue;
                    }
                    if ($key) {
                        $keywordDataValues['position'] = (int) $key[0]->position;
                        $keywordDataValues['clicks'] = (int) $key[0]->clicks;
                        $keywordDataValues['impression'] = $key[0]->impressions;
                    } else {
                        $keywordDataValues['position'] = 0;
                        $keywordDataValues['clicks'] = 0;
                        $keywordDataValues['impression'] = 0;
                    }
                }
    
                if (!empty($keywordDataValues)) {
                    KeywordData::updateOrCreate($keywordDataAttributes, $keywordDataValues);
                }
    
                $keyword->save();
            }
        }
    }
    
}
