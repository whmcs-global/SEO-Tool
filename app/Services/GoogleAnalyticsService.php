<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;

class GoogleAnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $keyFilePath = storage_path('app/dev-hosting-seekers-77eb4272e198.json');
        $this->propertyId = '325401964';

        $this->client = new BetaAnalyticsDataClient([
            'credentials' => $keyFilePath,
        ]);
    }

    // public function getPageVisitsForKeyword($keyword)
    // {
    //     // Create a filter to match the specific keyword
    //     $filter = new Filter([
    //         'field_name' => 'pagePath',
    //         'string_filter' => new Filter\StringFilter(['value' => $keyword])
    //     ]);

    //     $filterExpression = new FilterExpression([
    //         'filter' => $filter
    //     ]);

    //     // Build the request
    //     $request = new RunReportRequest([
    //         'property' => 'properties/' . $this->propertyId,
    //         'dimensions' => [new Dimension(['name' => 'pagePath'])],
    //         'metrics' => [new Metric(['name' => 'activeUsers'])],
    //         'dimension_filter' => $filterExpression
    //     ]);

    //     // Run the report
    //     $response = $this->client->runReport($request);

    //     // Process the response
    //     $results = [];
    //     foreach ($response->getRows() as $row) {
    //         $dimensionValues = $row->getDimensionValues();
    //         $metricValues = $row->getMetricValues();

    //         $results[] = [
    //             'pagePath' => $dimensionValues[0]->getValue(),
    //             'activeUsers' => $metricValues[0]->getValue()
    //         ];
    //     }

    //     return $results;
    // }

    public function getPageVisitsForKeyword($keyword)
    {
        // Create a filter to match the specific keyword
        $filter = new Filter([
            'field_name' => 'pagePath',
            'string_filter' => new Filter\StringFilter(['value' => $keyword])
        ]);

        $filterExpression = new FilterExpression([
            'filter' => $filter
        ]);

        // Build the request
        $request = new RunReportRequest([
            'property' => 'properties/' . $this->propertyId,
            'dimensions' => [new Dimension(['name' => 'pagePath'])],
            'metrics' => [new Metric(['name' => 'activeUsers'])],
            'date_ranges' => [new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today'])],
            'dimension_filter' => $filterExpression
        ]);

        // Run the report
        $response = $this->client->runReport([
            'property' => $request->getProperty(),
            'dimensions' => $request->getDimensions(),
            'metrics' => $request->getMetrics(),
            'dateRanges' => $request->getDateRanges(),
            'dimensionFilter' => $request->getDimensionFilter(),
        ]);

        // dd($response->getRows());
        // Process the response
        $results = [];
        foreach ($response->getRows() as $row) {
            $dimensionValues = $row->getDimensionValues();
            $metricValues = $row->getMetricValues();

            $results[] = [
                'pagePath' => $dimensionValues[0]->getValue(),
                'activeUsers' => $metricValues[0]->getValue()
            ];
        }

        return $results;
    }
}
