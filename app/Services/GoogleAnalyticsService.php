<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\MetricAggregation;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $keyFilePath = storage_path('app/dev-hosting-seekers-c4df9b6f2084.json');
        $this->propertyId = '325401964';

        $this->client = new BetaAnalyticsDataClient([
            'credentials' => $keyFilePath,
        ]);
    }

    public function getAllPageAnalyticsData($startDate, $endDate, $dimensionFilter = null)
    {
        try {
            $requestParams = [
                'property' => 'properties/' . $this->propertyId,
                'dimensions' => [
                    new Dimension(['name' => 'pagePath']),
                    new Dimension(['name' => 'pageTitle']),
                ],
                'metrics' => [
                    new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'newUsers']),
                    new Metric(['name' => 'totalUsers']),
                ],
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'metricAggregations' => [
                    MetricAggregation::TOTAL,
                ],
            ];

            if ($dimensionFilter) {
                $matchType = MatchType::MATCH_TYPE_UNSPECIFIED;
                switch ($dimensionFilter['match_type']) {
                    case 'EXACT':
                        $matchType = MatchType::EXACT;
                        break;
                    case 'BEGINS_WITH':
                        $matchType = MatchType::BEGINS_WITH;
                        break;
                    case 'ENDS_WITH':
                        $matchType = MatchType::ENDS_WITH;
                        break;
                    case 'CONTAINS':
                        $matchType = MatchType::CONTAINS;
                        break;
                }

                $requestParams['dimensionFilter'] = new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => $dimensionFilter['field_name'],
                        'string_filter' => new StringFilter([
                            'match_type' => $matchType,
                            'value' => $dimensionFilter['value'],
                            'case_sensitive' => $dimensionFilter['case_sensitive'],
                        ]),
                    ]),
                ]);
            }

            $response = $this->client->runReport($requestParams);

            $totals = [];
            foreach ($response->getTotals() as $totalRow) {
                $totals[] = [
                    'activeUsers' => $totalRow->getMetricValues()[0]->getValue(),
                    'newUsers' => $totalRow->getMetricValues()[1]->getValue(),
                    'totalUsers' => $totalRow->getMetricValues()[2]->getValue(),
                ];
            }

            $results = [];
            foreach ($response->getRows() as $row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                $results[] = [
                    'pagePath' => $dimensionValues[0]->getValue(),
                    'pageTitle' => $dimensionValues[1]->getValue(),
                    'activeUsers' => $metricValues[0]->getValue(),
                    'newUsers' => $metricValues[1]->getValue(),
                    'totalUsers' => $metricValues[2]->getValue(),
                ];
            }

            return [
                'results' => $results,
                'totals' => $totals[0] ?? ['activeUsers' => 0, 'newUsers' => 0, 'totalUsers' => 0],
            ];
        } catch (Exception $e) {
            Log::error('Google Analytics API Error: ' . $e->getMessage());
            return ['error' => 'An error occurred while fetching data. Please try again later.'];
        }
    }

    public function getAllPageOrganicTrafficAnalyticsData($startDate, $endDate, $dimensionFilter = null)
    {
        try {
            $requestParams = [
                'property' => 'properties/' . $this->propertyId,
                'dimensions' => [
                    new Dimension(['name' => 'landingPagePlusQueryString']),
                ],
                'metrics' => [
                    new Metric(['name' => 'organicGoogleSearchClicks']),
                    new Metric(['name' => 'organicGoogleSearchImpressions']),
                    new Metric(['name' => 'organicGoogleSearchClickThroughRate']),
                    new Metric(['name' => 'organicGoogleSearchAveragePosition']),
                ],
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'metricAggregations' => [
                    MetricAggregation::TOTAL,
                ],
            ];

            if ($dimensionFilter) {
                $matchType = MatchType::MATCH_TYPE_UNSPECIFIED;
                switch ($dimensionFilter['match_type']) {
                    case 'EXACT':
                        $matchType = MatchType::EXACT;
                        break;
                    case 'BEGINS_WITH':
                        $matchType = MatchType::BEGINS_WITH;
                        break;
                    case 'ENDS_WITH':
                        $matchType = MatchType::ENDS_WITH;
                        break;
                    case 'CONTAINS':
                        $matchType = MatchType::CONTAINS;
                        break;
                }

                $requestParams['dimensionFilter'] = new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => $dimensionFilter['field_name'],
                        'string_filter' => new StringFilter([
                            'match_type' => $matchType,
                            'value' => $dimensionFilter['value'],
                            'case_sensitive' => $dimensionFilter['case_sensitive'],
                        ]),
                    ]),
                ]);
            }

            $response = $this->client->runReport($requestParams);
            $totals = [];
            if (count($response->getTotals()) > 0) {
                foreach ($response->getTotals() as $totalRow) {
                    $totals[] = [
                        'organicGoogleSearchClicks' => $totalRow->getMetricValues()[0]->getValue() ?? 0,
                        'organicGoogleSearchImpressions' => $totalRow->getMetricValues()[1]->getValue() ?? 0,
                        'organicGoogleSearchClickThroughRate' => $totalRow->getMetricValues()[2]->getValue() ?? 0,
                        'organicGoogleSearchAveragePosition' => $totalRow->getMetricValues()[3]->getValue() ?? 0,
                    ];
                }
            }

            $results = [];
            if (count($response->getRows()) > 0) {
                foreach ($response->getRows() as $row) {
                    $dimensionValues = $row->getDimensionValues();
                    $metricValues = $row->getMetricValues();

                    $results[] = [
                        'landingPagePlusQueryString' => $dimensionValues[0]->getValue() ?? '',
                        'organicGoogleSearchClicks' => $metricValues[0]->getValue() ?? 0,
                        'organicGoogleSearchImpressions' => $metricValues[1]->getValue() ?? 0,
                        'organicGoogleSearchClickThroughRate' => $metricValues[2]->getValue() ?? 0,
                        'organicGoogleSearchAveragePosition' => $metricValues[3]->getValue() ?? 0,
                    ];
                }
            }

            return [
                'results' => $results,
                'totals' => $totals[0] ?? [
                    'organicGoogleSearchClicks' => 0,
                    'organicGoogleSearchImpressions' => 0,
                    'organicGoogleSearchClickThroughRate' => 0,
                    'organicGoogleSearchAveragePosition' => 0,
                ],
            ];
        } catch (Exception $e) {
            Log::error('Google Analytics API Error: ' . $e->getMessage());
            return ['error' => 'An error occurred while fetching data. Please try again later.'];
        }
    }


    public function getPageDetails($startDate, $endDate, $url)
    {
        try {
            $requestParams = [
                'property' => 'properties/' . $this->propertyId,
                'dimensions' => [
                    new Dimension(['name' => 'pagePath']),
                    new Dimension(['name' => 'sessionSource']),
                    new Dimension(['name' => 'sessionSourceMedium']),
                ],
                'metrics' => [
                    // new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'newUsers']),
                    new Metric(['name' => 'totalUsers']),
                ],
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'metricAggregations' => [
                    MetricAggregation::TOTAL,
                ],
            ];

            if ($url) {
                $requestParams['dimensionFilter'] = new FilterExpression([
                    'filter' => new Filter([
                        'field_name' => 'pagePath',
                        'string_filter' => new StringFilter([
                            'match_type' => MatchType::EXACT,
                            'value' => $url,
                            'case_sensitive' => false,
                        ]),
                    ]),
                ]);
            }

            $response = $this->client->runReport($requestParams);

            $totals = [];
            foreach ($response->getTotals() as $totalRow) {
                $totals[] = [
                    'newUsers' => $totalRow->getMetricValues()[0]->getValue(),
                    'totalUsers' => $totalRow->getMetricValues()[1]->getValue(),
                ];
            }

            $results = [];
            foreach ($response->getRows() as $row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                $results[] = [
                    'pagePath' => $dimensionValues[0]->getValue(),
                    'sessionSource'=> $dimensionValues[1]->getValue(),
                    'sessionSourceMedium' => $dimensionValues[2]->getValue(),
                    'newUsers' => $metricValues[0]->getValue(),
                    'totalUsers' => $metricValues[1]->getValue(),
                ];
            }

            return [
                'results' => $results,
                'totals' => $totals[0] ?? ['newUsers' => 0, 'totalUsers' => 0],
            ];
        } catch (Exception $e) {
            Log::error('Google Analytics API Error: ' . $e->getMessage());
            return ['error' => 'An error occurred while fetching data. Please try again later.'];
        }
    }

}
