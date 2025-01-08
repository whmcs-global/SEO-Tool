<?php

namespace App\Services;

use App\Models\Website;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $keyFilePath = storage_path('app/dev-hosting-seekers-c4df9b6f2084.json');
        $property_id = Website::where('id', auth()->user()->website_id)->value('property_id');
        $this->propertyId = $property_id;

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
                    // new Dimension(['name' => 'sessionSourceMedium']),
                    // new Dimension(['name' => 'sessionSource']),
                ],
                'metrics' => [
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
                $metricValues = $totalRow->getMetricValues();
                $totals[] = [
                    'newUsers' => isset($metricValues[0]) ? $metricValues[0]->getValue() : 0,
                    'totalUsers' => isset($metricValues[1]) ? $metricValues[1]->getValue() : 0,
                ];
            }

            $results = [];
            foreach ($response->getRows() as $row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                $result = [
                    'pagePath' => isset($dimensionValues[0]) ? $dimensionValues[0]->getValue() : '',
                    'pageTitle' => isset($dimensionValues[1]) ? $dimensionValues[1]->getValue() : '',
                    // 'sessionSourceMedium' => isset($dimensionValues[2]) ? $dimensionValues[2]->getValue() : '',
                    // 'sessionSource' => isset($dimensionValues[3]) ? $dimensionValues[3]->getValue() : '',
                    'newUsers' => isset($metricValues[0]) ? $metricValues[0]->getValue() : 0,
                    'totalUsers' => isset($metricValues[1]) ? $metricValues[1]->getValue() : 0,
                ];

                if (array_filter($result)) {
                    $results[] = $result;
                }
            }

            return [
                'results' => $results,
                'totals' => !empty($totals) ? $totals[0] : ['newUsers' => 0, 'totalUsers' => 0],
            ];
        } catch (Exception $e) {
            return [
                'error' => 'An error occurred while fetching data. Please try again later.',
                'details' => $e->getMessage()
            ];
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
                    'sessionSource' => $dimensionValues[1]->getValue(),
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


    public function analyticsGraph($startDate1, $endDate1, $startDate2 = null, $endDate2 = null)
    {
        try {
            $cacheKey = 'analytics_graph_' . md5($startDate1 . $endDate1 . $startDate2 . $endDate2 . auth()->user()->website_id);

            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                return $cachedData;
            }
            $dimensions = [
                new Dimension(['name' => 'date']),
            ];

            $metrics = [
                new Metric(['name' => 'newUsers']),
                new Metric(['name' => 'totalUsers']),
            ];

            $dateRanges = [
                new DateRange(['start_date' => $startDate1, 'end_date' => $endDate1]),
            ];

            if ($startDate2 && $endDate2) {
                $dateRanges[] = new DateRange(['start_date' => $startDate2, 'end_date' => $endDate2]);
            }

            $metricAggregations = [MetricAggregation::TOTAL];

            $requestParams = [
                'property' => 'properties/' . $this->propertyId,
                'dimensions' => $dimensions,
                'metrics' => $metrics,
                'dateRanges' => $dateRanges,
                'metricAggregations' => $metricAggregations,
            ];

            $response = $this->client->runReport($requestParams);

            $totals = [];
            if ($startDate2 && $endDate2) {
                foreach ($response->getTotals() as $index => $totalRow) {
                    $totals[$totalRow->getDimensionValues()[1]->getValue()] = [
                        'newUsers' => $totalRow->getMetricValues()[0]->getValue(),
                        'totalUsers' => $totalRow->getMetricValues()[1]->getValue(),
                    ];
                }
            } else {
                foreach ($response->getTotals() as $index => $totalRow) {
                    $totals['date_range_0'] = [
                        'newUsers' => $totalRow->getMetricValues()[0]->getValue(),
                        'totalUsers' => $totalRow->getMetricValues()[1]->getValue(),
                    ];
                }
            }

            $results = [];
            foreach ($response->getRows() as $row) {
                $dimensionValues = $row->getDimensionValues();
                $metricValues = $row->getMetricValues();

                $results[] = [
                    'date' => $dimensionValues[0]->getValue(),
                    'newUsers' => $metricValues[0]->getValue(),
                    'totalUsers' => $metricValues[1]->getValue(),
                ];
            }

            $dataToCache = [
                'results' => $results,
                'totals' => $totals,
            ];

            Cache::put($cacheKey, $dataToCache, now()->addMinutes(240));

            return $dataToCache;
        } catch (Exception $e) {
            return ['error' => 'An error occurred while fetching data. Please try again later.'];
        }
    }

    /**
     * Retrieve Google Analytics data matching the exact response structure
     * Response Cached for 4 hours
     * @param string|null $startDate Start date in 'Y-m-d' format (optional)
     * @param string|null $endDate End date in 'Y-m-d' format (optional)
     * @return array Google Analytics data in specified JSON format
     */
    public function getSessionSourceMediumUserData(?string $startDate = null, ?string $endDate = null)
    {
        try {
            $startDate = $startDate ?? now()->subDays(28)->format('Y-m-d');
            $endDate = $endDate ?? today()->subDays(1)->format('Y-m-d');

            $cacheKey = "ga_session_source_medium_{$this->propertyId}_{$startDate}_{$endDate}";

            return Cache::remember($cacheKey, now()->addHours(4), function () use ($startDate, $endDate) {
                if (!$this->propertyId) {
                    throw new Exception('No Google Analytics property ID found');
                }

                $dateRange = new DateRange([
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);

                $sessionMediumDimension = new Dimension(['name' => 'sessionMedium']);
                $sessionSourceDimension = new Dimension(['name' => 'sessionSource']);

                $newUsersMetric = new Metric(['name' => 'newUsers']);
                $totalUsersMetric = new Metric(['name' => 'totalUsers']);
                $metricAggregations = [MetricAggregation::TOTAL];
                $request = [
                    'property' => "properties/{$this->propertyId}",
                    'dateRanges' => [$dateRange],
                    'dimensions' => [$sessionMediumDimension, $sessionSourceDimension],
                    'metrics' => [$newUsersMetric, $totalUsersMetric],
                    'metricAggregations' => $metricAggregations,
                ];

                $response = $this->client->runReport($request);

                $result = [
                    'dimensionHeaders' => [
                        ['name' => 'sessionMedium'],
                        ['name' => 'sessionSource']
                    ],
                    'metricHeaders' => [
                        ['name' => 'newUsers', 'type' => 'TYPE_INTEGER'],
                        ['name' => 'totalUsers', 'type' => 'TYPE_INTEGER']
                    ],
                    'rows' => [],
                    'totals' => [],
                    'rowCount' => 0,
                    'metadata' => [
                        'currencyCode' => 'USD',
                        'timeZone' => 'Asia/Calcutta'
                    ],
                    'kind' => 'analyticsData#runReport'
                ];

                $rows = $response->getRows();
                if ($rows) {
                    foreach ($rows as $row) {
                        $dimensionValues = $row->getDimensionValues();
                        $metricValues = $row->getMetricValues();

                        if (
                            count($dimensionValues) >= 2 &&
                            count($metricValues) >= 2
                        ) {
                            $result['rows'][] = [
                                'dimensionValues' => [
                                    ['value' => $dimensionValues[0]->getValue() ?? ''],
                                    ['value' => $dimensionValues[1]->getValue() ?? '']
                                ],
                                'metricValues' => [
                                    ['value' => (string)($metricValues[0]->getValue() ?? 0)],
                                    ['value' => (string)($metricValues[1]->getValue() ?? 0)]
                                ]
                            ];
                        }
                    }
                }

                $result['rowCount'] = count($result['rows']);

                $totals = $response->getTotals();
                if ($totals && count($totals) > 0) {
                    $totalRow = $totals[0];
                    $totalMetricValues = $totalRow->getMetricValues();

                    if (count($totalMetricValues) >= 2) {
                        $result['totals'][] = [
                            'dimensionValues' => [
                                ['value' => 'RESERVED_TOTAL'],
                                ['value' => 'RESERVED_TOTAL']
                            ],
                            'metricValues' => [
                                ['value' => (string)($totalMetricValues[0]->getValue() ?? 0)],
                                ['value' => (string)($totalMetricValues[1]->getValue() ?? 0)]
                            ]
                        ];
                    }
                }

                if (empty($result['rows'])) {
                    Log::warning('Google Analytics: No data retrieved', [
                        'property_id' => $this->propertyId,
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]);
                }

                return $result;
            });
        } catch (Exception $e) {
            Log::error('Google Analytics Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'property_id' => $this->propertyId ?? 'N/A',
                'start_date' => $startDate ?? 'N/A',
                'end_date' => $endDate ?? 'N/A'
            ]);

            return [
                'error' => true,
                'message' => $e->getMessage(),
                'property_id' => $this->propertyId
            ];
        }
    }
}
