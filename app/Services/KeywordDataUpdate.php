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

    public function update($cron_id = null)
    {
        $this->cron_id = $cron_id;
        $keywords = Keyword::all();

        if ($keywords->isEmpty()) {
            return;
        }

        $keywords_ads = $keywords->pluck('keyword')->toArray();
        $countries = Country::all();

        foreach ($countries as $country) {
            try {
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

                        // Log API error using ExternalApiLogger
                        if (isset($key['code'])) {
                            $error_message = $key['message'];
                            Log::error('line 61 keyworddata '.$error_message);
                            ExternalApiLogger::log(
                                $this->cron_id,
                                'Google Keyword Data Fetch',
                                $error_message,
                                'https://searchconsole.googleapis.com/webmasters/v3',
                                'POST',
                                json_encode(['keyword' => $keyword->keyword, 'country' => $country->ISO_CODE]),
                                json_encode($key),
                                $key['code']
                            );
                            return false;
                            // continue;
                        }

                        if (isset($key['error'])) {
                            Log::error('line 65 keyworddata '.$key['error']);
                            ExternalApiLogger::log(
                                $this->cron_id,
                                'Google Keyword Data Fetch',
                                $key['error'],
                                'https://searchconsole.googleapis.com/webmasters/v3',
                                'POST',
                                json_encode(['keyword' => $keyword->keyword, 'country' => $country->ISO_CODE]),
                                json_encode($key),
                                $key['code'] ?? 500 
                            );

                            return false;
                            // continue;
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

                    return true;
                }
            } catch (\Exception $e) {
                // Catch any unexpected errors and log them with ExternalApiLogger
                Log::error('Error updating keyword data: ' . $e->getMessage());
                ExternalApiLogger::log(
                    $this->cron_id,
                    'Google Keyword Historical Metrics Fetch',
                    $e->getMessage(),
                    'Google Ads API endpoint', // Add the correct API endpoint here
                    'GET',
                    json_encode(['keywords' => $keywords_ads, 'country' => $country->Google_Code]),
                    $e->getMessage(),
                    $e->getCode()
                );

                return false;
            }
        }
    }
}

