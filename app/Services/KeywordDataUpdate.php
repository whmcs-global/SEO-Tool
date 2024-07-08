<?php
namespace App\Services;

use App\Services\GoogleAdsService;
use Illuminate\Http\Request;
use App\Traits\KeywordAnalytic;
use App\Models\{Keyword, AdminSetting, Label, keyword_label, Country, KeywordData};
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
                    $key = $this->keywords(request(), $keyword, $country->ISO_CODE);
                    if (isset($key['code'])) {
                        $error_message = $key['message'];
                        Log::error('line 61 keyworddata '.$error_message);
                        continue;
                    }
                    if (isset($key['error'])) {
                        Log::error('line 65 keyworddata '.$key['error']);
                        continue;
                    }
                    if ($key) {
                        $keywordDataValues['position'] = isset($key[0]['position']) ? (int) $key[0]['position'] : null;
                        $keywordDataValues['clicks'] = isset($key[0]['clicks']) ? (int) $key[0]['clicks'] : 0;
                        $keywordDataValues['impression'] = isset($key[0]['impressions']) ? $key[0]['impressions'] : 0;
                    } else {
                        $keywordDataValues['position'] = null;
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
