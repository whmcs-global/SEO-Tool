<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleAnalyticsService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;


class TrackPagesController extends Controller
{

    public function pageDetails(Request $request)
    {
        try {
            $startDate = $request->start_date ?? 'yesterday';
            $endDate = $request->end_date ?? 'yesterday';
            $url = $request->url ?? '/';

            $analyticsService = new GoogleAnalyticsService();
            $pageAnalyticsData = $analyticsService->getPageDetails($startDate, $endDate, $url);

            $pageReport = $pageAnalyticsData['results'] ?? [];
            $pageTotals = $pageAnalyticsData['totals'] ?? ['newUsers' => 0, 'totalUsers' => 0];

            return view('track_pages.details', compact(
                'pageReport',
                'pageTotals',
                'startDate',
                'endDate',
                'url'
            ));
        } catch (Exception $e) {
            Log::error('Page Details Error: ' . $e->getMessage());

            return back()->with('error', 'Failed to fetch analytics data. Please try again later.');
        }
    }

    public function list(Request $request)
    {
        $daterange = $request->input('daterange');
        $pagePathFilter = $request->input('pagePath');
        $matchType = $request->input('matchType', 'CONTAINS');
        $website_id = auth()->user()->website_id;
        if ($daterange) {
            [$startDate, $endDate] = explode(' - ', $daterange);
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } else {
            $startDate = 'yesterday';
            $endDate = 'yesterday';
        }

        $cacheKey = "analytics_data_{$startDate}_{$endDate}_{$pagePathFilter}_{$website_id}";

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            $pageReport = $data['pageReport'];
            $pageTotals = $data['pageTotals'];
            $organicReport = $data['organicReport'];
            $organicTotals = $data['organicTotals'];
        } else {
            $analyticsService = new GoogleAnalyticsService();

            $dimensionFilter = $pagePathFilter ? [
                'field_name' => 'pagePath',
                'match_type' => $matchType,
                'value' => $pagePathFilter,
                'case_sensitive' => true,
            ] : null;

            $organicDimensionFilter = $pagePathFilter ? [
                'field_name' => 'landingPagePlusQueryString',
                'match_type' => $matchType,
                'value' => $pagePathFilter,
                'case_sensitive' => true,
            ] : null;

            $pageAnalyticsData = $analyticsService->getAllPageAnalyticsData($startDate, $endDate, $dimensionFilter);
            $pageReport = $pageAnalyticsData['results'] ?? [];
            $pageTotals = $pageAnalyticsData['totals'] ?? ['activeUsers' => 0, 'newUsers' => 0, 'totalUsers' => 0];

            $organicTrafficData = $analyticsService->getAllPageOrganicTrafficAnalyticsData($startDate, $endDate, $organicDimensionFilter);
            $organicReport = $organicTrafficData['results'] ?? [];
            $organicTotals = $organicTrafficData['totals'] ?? [
                'organicGoogleSearchClicks' => 0,
                'organicGoogleSearchImpressions' => 0,
                'organicGoogleSearchClickThroughRate' => 0,
                'organicGoogleSearchAveragePosition' => 0,
            ];

            Cache::put($cacheKey, [
                'pageReport' => $pageReport,
                'pageTotals' => $pageTotals,
                'organicReport' => $organicReport,
                'organicTotals' => $organicTotals
            ], now()->addMinutes(60));
        }
        $mergedReport = $this->mergePageAndOrganicData($pageReport, $organicReport);

        if ($request->ajax()) {
            if (!empty($mergedReport)) {
                return view('track_pages.partials.analytics_table', compact('mergedReport', 'pageTotals', 'organicTotals', 'startDate', 'endDate'))->render();
            } else {
                return response()->json(['data' => 'false']);
            }
        }

        return view('track_pages.list', compact('mergedReport', 'pageTotals', 'organicTotals'));
    }

    private function mergePageAndOrganicData($pageReport, $organicReport)
    {
        $mergedReport = [];
        foreach ($pageReport as $page) {
            $mergedData = [
                'pagePath' => $page['pagePath'],
                'pageTitle' => $page['pageTitle'],
                'newUsers' => $page['newUsers'],
                'totalUsers' => $page['totalUsers'],
                'sessionSourceMedium' => $page['sessionSourceMedium'],
                'sessionSource' => $page['sessionSource'],
                'organicGoogleSearchClicks' => 0,
                'organicGoogleSearchImpressions' => 0,
                'organicGoogleSearchClickThroughRate' => 0,
                'organicGoogleSearchAveragePosition' => 0
            ];

            foreach ($organicReport as $organic) {
                if ($organic['landingPagePlusQueryString'] == $page['pagePath']) {
                    $mergedData['organicGoogleSearchClicks'] = $organic['organicGoogleSearchClicks'];
                    $mergedData['organicGoogleSearchImpressions'] = $organic['organicGoogleSearchImpressions'];
                    $mergedData['organicGoogleSearchClickThroughRate'] = $organic['organicGoogleSearchClickThroughRate'];
                    $mergedData['organicGoogleSearchAveragePosition'] = $organic['organicGoogleSearchAveragePosition'];
                    break;
                }
            }

            $mergedReport[] = $mergedData;
        }

        return $mergedReport;
    }
}
