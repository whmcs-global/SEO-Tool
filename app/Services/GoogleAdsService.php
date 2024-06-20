<?php

namespace App\Services;

use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V16\ResourceNames;
use Google\Ads\GoogleAds\V16\Enums\KeywordPlanNetworkEnum\KeywordPlanNetwork;
use Google\Ads\GoogleAds\V16\Services\GenerateKeywordHistoricalMetricsRequest;
use Google\ApiCore\ApiException;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Log;

class GoogleAdsService
{
    private $client;
    private $adminSetting;

    public function __construct()
    {
        $this->adminSetting = AdminSetting::where('type', 'google_ads')->first();
        if (!$this->adminSetting) {
            throw new \Exception("Google Ads settings not found");
        }
        $this->initializeClient();
    }

    private function initializeClient()
    {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId(config('google-ads.client_id'))
            ->withClientSecret(config('google-ads.client_secret'))
            ->withRefreshToken($this->adminSetting->refresh_token)
            ->build();

        $this->client = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(config('google-ads.developer_token'))
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId('5256032344')
            ->build();
    }

    private function refreshAccessToken()
    {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId(config('google-ads.client_id'))
            ->withClientSecret(config('google-ads.client_secret'))
            ->withRefreshToken($this->adminSetting->refresh_token)
            ->build();
            
        try {
            $oAuth2Credential->fetchAuthToken();
            $this->adminSetting->refresh_token = $oAuth2Credential->getRefreshToken();
            $this->adminSetting->save();
        } catch (\Exception $e) {
            Log::error("Failed to refresh OAuth2 token: " . $e->getMessage());
            throw new \Exception("Failed to refresh OAuth2 token: " . $e->getMessage());
        }

        $this->client = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(config('google-ads.developer_token'))
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId('5256032344')
            ->build();
    }

    public function getKeywordHistoricalMetrics($keywords , $location_id)
    {   
        $keywordPlanIdeaServiceClient = $this->client->getKeywordPlanIdeaServiceClient();

        try {
            $response = $keywordPlanIdeaServiceClient->generateKeywordHistoricalMetrics(
                new GenerateKeywordHistoricalMetricsRequest([
                    'customer_id' => config('google-ads.login_customer_id'),
                    'keywords' => $keywords,
                    'geo_target_constants' => [ResourceNames::forGeoTargetConstant($location_id)],
                    'keyword_plan_network' => KeywordPlanNetwork::GOOGLE_SEARCH,
                    'language' => ResourceNames::forLanguageConstant(1000)
                ])
            );

            $results = $response->getResults();
            $modifiedResults = [];
            foreach ($results as $result) {
                $metrics = $result->getKeywordMetrics();
                if (!$metrics) continue;
                $lowBidMicros = $metrics->getLowTopOfPageBidMicros() ?? 0;
                $lowBidRupees = $lowBidMicros / 1000000;
                $highBidMicros = $metrics->getHighTopOfPageBidMicros() ?? 0;
                $highBidRupees = $highBidMicros / 1000000;
                $modifiedResults[] = [
                    'text' => $result->getText(),
                    'keywordMetrics' => [
                        'avgMonthlySearches' => $metrics->getAvgMonthlySearches() ?? 0,
                        'monthlySearchVolumes' => $metrics->getMonthlySearchVolumes() ?? [],
                        'competition' => $metrics->getCompetition() ?? null,
                        'competitionIndex' => $metrics->getCompetitionIndex() ?? null,
                        'lowTopOfPageBidRupees' => $lowBidRupees,
                        'highTopOfPageBidRupees' => $highBidRupees
                    ]
                ];
            }
            return $modifiedResults;
        } catch (ApiException $apiException) {
            if ($apiException->getStatus() === 'UNAUTHENTICATED') {
                $this->refreshAccessToken();
                return $this->getKeywordHistoricalMetrics($keywords);
            }
            throw new \Exception("ApiException was thrown with message: " . $apiException->getMessage());
        }
    }
}
