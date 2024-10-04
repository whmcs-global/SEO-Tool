<?php

namespace App\Services;

use App\Services\GoogleAdsService;
use Illuminate\Http\Request;
use App\Traits\KeywordAnalytic;
use App\Models\{Keyword, AdminSetting, Label, keyword_label, Country, KeywordData};
use Illuminate\Support\Facades\Log;
use App\Services\ExternalApiLogger;

class KeywordDataUpdate
{
    use KeywordAnalytic;
    protected $cron_id;
    protected $googleAdsService;

    public function __construct(GoogleAdsService $googleAdsService)
    {
        $this->googleAdsService = $googleAdsService;
    }

    // public function update($cron_id = null)
    // {
    //     $this->cron_id = $cron_id;
    //     $keywords = Keyword::all();

    //     if ($keywords->isEmpty()) {
    //         return;
    //     }

    //     $keywords_ads = $keywords->pluck('keyword')->toArray();
    //     $countries = Country::all();

    //     foreach ($countries as $country) {
    //         try {
    //             $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords_ads, $country->Google_Code);
    //             $metricsMap = collect($metrics)->pluck('keywordMetrics', 'text');

    //             foreach ($keywords as $keyword) {
    //                 $keywordDataAttributes = [
    //                     'keyword_id' => $keyword->id,
    //                     'country_id' => $country->id,
    //                 ];

    //                 $keywordDataValues = $this->getKeywordDataValues($metricsMap, $keyword);

    //                 $settings = AdminSetting::where('website_id', $keyword->website_id)
    //                     ->where('type', 'google')
    //                     ->get();

    //                 if ($settings) {
    //                     $key = $this->keywords(request(), $keyword, $country->ISO_CODE);

    //                     if ($this->handleApiErrors($key, $keyword, $country)) {
    //                         continue;
    //                     }

    //                     $keywordDataValues = array_merge($keywordDataValues, $this->getAdditionalKeywordData($key));
    //                 }

    //                 if (!empty($keywordDataValues)) {
    //                     KeywordData::updateOrCreate($keywordDataAttributes, $keywordDataValues);
    //                 }

    //                 $keyword->save();
    //             }
    //         } catch (\Exception $e) {
    //             $this->logException($e, $keywords_ads, $country);
    //             return false;
    //         }
    //     }

    //     return true;
    // }
    public function update($cron_id = null)
    {
        $this->cron_id = $cron_id;

        // Fetch all countries once
        $countries = Country::all();

        // Process keywords in chunks of 25
        Keyword::chunk(25, function ($keywords) use ($countries) {
            if ($keywords->isEmpty()) {
                return;
            }

            $keywords_ads = $keywords->pluck('keyword')->toArray();

            foreach ($countries as $country) {
                try {
                    // Fetch metrics for the current chunk of keywords
                    $metrics = $this->googleAdsService->getKeywordHistoricalMetrics($keywords_ads, $country->Google_Code);
                    $metricsMap = collect($metrics)->pluck('keywordMetrics', 'text');

                    foreach ($keywords as $keyword) {
                        $keywordDataAttributes = [
                            'keyword_id' => $keyword->id,
                            'country_id' => $country->id,
                        ];

                        // Get values for the keyword
                        $keywordDataValues = $this->getKeywordDataValues($metricsMap, $keyword);

                        // Fetch settings for the current keyword
                        $settings = AdminSetting::where('website_id', $keyword->website_id)
                            ->where('type', 'google')
                            ->get();

                        if ($settings) {
                            $key = $this->keywords(request(), $keyword, $country->ISO_CODE);

                            if ($this->handleApiErrors($key, $keyword, $country)) {
                                continue;
                            }

                            $keywordDataValues = array_merge($keywordDataValues, $this->getAdditionalKeywordData($key));
                        }

                        // Update or create the keyword data entry
                        if (!empty($keywordDataValues)) {
                            KeywordData::updateOrCreate($keywordDataAttributes, $keywordDataValues);
                        }

                        $keyword->save();
                    }
                } catch (\Exception $e) {
                    $this->logException($e, $keywords_ads, $country);
                    return false;
                }
            }
        });

        return true;
    }


    private function getKeywordDataValues($metricsMap, $keyword)
    {
        if (isset($metricsMap[$keyword->keyword])) {
            $metric = $metricsMap[$keyword->keyword];
            return [
                'search_volume' => $metric['avgMonthlySearches'],
                'competition' => $metric['competition'],
                'bid_rate_low' => $metric['lowTopOfPageBidRupees'],
                'bid_rate_high' => $metric['highTopOfPageBidRupees'],
            ];
        }

        return [];
    }

    private function handleApiErrors($key, $keyword, $country)
    {
        if (isset($key['code']) || isset($key['error'])) {
            $keywords = json_encode($keyword->keyword);
            $error_message = $key['message'] ?? $key['error'];
            Log::error('Keyword data error: ' . $error_message);
            ExternalApiLogger::log(
                $this->cron_id,
                'Google Keyword Data Fetch',
                $error_message,
                'https://searchconsole.googleapis.com/webmasters/v3',
                'POST',
                json_encode(['keyword' => $keywords, 'country' => $country->ISO_CODE]),
                json_encode($key),
                $key['code'] ?? 500
            );
            return true;
        }

        return false;
    }

    private function getAdditionalKeywordData($key)
    {
        return [
            'position' => isset($key[0]['position']) ? (int) $key[0]['position'] : null,
            'clicks' => isset($key[0]['clicks']) ? (int) $key[0]['clicks'] : 0,
            'impression' => isset($key[0]['impressions']) ? $key[0]['impressions'] : 0,
        ];
    }

    private function logException($e, $keywords_ads, $country)
    {
        $keywords = json_encode($keywords_ads);
        Log::error('Error updating keyword data: ' . $e->getMessage());
        ExternalApiLogger::log(
            $this->cron_id,
            'Google Keyword Historical Metrics Fetch',
            $e->getMessage(),
            'Google Ads API endpoint',
            'GET',
            json_encode(['keywords' => $keywords, 'country' => $country->Google_Code]),
            $e->getMessage(),
            $e->getCode()
        );
    }
}
