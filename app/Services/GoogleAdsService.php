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
            ->withClientId('304351325631-nd7eg4gu95mlo88cc6fps15e9l75aik1.apps.googleusercontent.com')
            ->withClientSecret('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')
            ->withRefreshToken('1//05KcT-9ntcRdjCgYIARAAGAUSNwF-L9Ir5GG3hN5bQu5zkuXEb9i9aS5Wf6jKNfcsJi7FQyE6lGXZmkdHg33h2zpxkax2E_wzj7I')
            ->build();

        $this->client = (new GoogleAdsClientBuilder())
            ->withDeveloperToken('ZEEd2ZULXzdAsDnHPrqF4g')
            ->withOAuth2Credential($oAuth2Credential)
            ->withLoginCustomerId('9663882815')
            ->build();
    }


    public function getKeywordHistoricalMetrics($keywords)
    {
        $keywordPlanIdeaServiceClient = $this->client->getKeywordPlanIdeaServiceClient();

        try {
            $response = $keywordPlanIdeaServiceClient->generateKeywordHistoricalMetrics(
                new GenerateKeywordHistoricalMetricsRequest([
                    'customer_id' => '9663882815',
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
