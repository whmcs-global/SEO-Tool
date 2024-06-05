<?php

namespace App\Services;

use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClient;
use Google\Ads\GoogleAds\Lib\V16\GoogleAdsClientBuilder;
use Google\Ads\GoogleAds\Lib\OAuth2TokenBuilder;
use Google\Ads\GoogleAds\Util\V16\ResourceNames;
use Google\Ads\GoogleAds\V16\Enums\KeywordPlanNetworkEnum\KeywordPlanNetwork;
use Google\Ads\GoogleAds\V16\Services\GenerateKeywordHistoricalMetricsRequest;
use Google\ApiCore\ApiException;

class GoogleAdsService
{
    private $client;

    public function __construct()
    {
        $oAuth2Credential = (new OAuth2TokenBuilder())
            ->withClientId('')
            ->withClientSecret('')
            ->withRefreshToken('')
            ->build();

        $this->client = (new GoogleAdsClientBuilder())
            ->withDeveloperToken('')
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId('')
            ->build();
    }


    public function getKeywordHistoricalMetrics($keywords)
    {
        $keywordPlanIdeaServiceClient = $this->client->getKeywordPlanIdeaServiceClient();

        try {
            $response = $keywordPlanIdeaServiceClient->generateKeywordHistoricalMetrics(
                new GenerateKeywordHistoricalMetricsRequest([
                    'customer_id' => '',
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
