<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\MetricAggregation;
use Google\Analytics\Data\V1beta\DateRange;
use Illuminate\Support\Facades\Log;
use Exception;

class GoogleAnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $keyFilePath = storage_path('app/dev-hosting-seekers-c4df9b6f2084.json');
        $this->propertyId = '256171637';

        $this->client = new BetaAnalyticsDataClient([
            'credentials' => $keyFilePath,
        ]);
    }

    public function getAllPageAnalyticsData($startDate, $endDate)
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

}
