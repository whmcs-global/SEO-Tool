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

class GoogleAdsService
{
    private $client;

    public function __construct()
    {
        $googleads = AdminSetting::where('website_id', auth()->user()->website_id)->where('type','google_ads')->first();
        if(!$googleads){
            throw new \Exception("Google Ads settings not found");
        }
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId(config('google-ads.client_id'))
            ->withClientSecret(config('google-ads.client_secret'))
            ->withRefreshToken($googleads->refresh_token)
            ->build();

        $this->client = (new GoogleAdsClientBuilder())
            ->withDeveloperToken(config('google-ads.developer_token'))
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId(config('google-ads.login_customer_id'))
            ->build();
    }


    public function getKeywordHistoricalMetrics($keywords)
    {
        $keywordPlanIdeaServiceClient = $this->client->getKeywordPlanIdeaServiceClient();

        try {
            $response = $keywordPlanIdeaServiceClient->generateKeywordHistoricalMetrics(
                new GenerateKeywordHistoricalMetricsRequest([
                    'customer_id' => config('google-ads.login_customer_id'),
                    'keywords' => $keywords,
                    'geo_target_constants' => [ResourceNames::forGeoTargetConstant(2840)],
                    'keyword_plan_network' => KeywordPlanNetwork::GOOGLE_SEARCH,
                    'language' => ResourceNames::forLanguageConstant(1000)
                ])
            );

            dd($response->getResults());
        } catch (ApiException $apiException) {
            throw new \Exception("ApiException was thrown with message: " . $apiException->getMessage());
        }
    }
}
