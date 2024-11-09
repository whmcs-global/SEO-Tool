<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleAnalyticsService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TrackPagesController extends Controller
{

    public function list(Request $request)
    {
        $daterange = $request->input('daterange');
        $pagePathFilter = $request->input('pagePath');
        $matchType = $request->input('matchType', 'CONTAINS');

        if ($daterange) {
            [$startDate, $endDate] = explode(' - ', $daterange);
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } else {
            $startDate = Carbon::yesterday()->format('Y-m-d');
            $endDate = $startDate;
        }

        $cacheKey = 'analytics_data_' . md5($startDate . '_' . $endDate . '_' . $pagePathFilter . '_' . $matchType);

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($startDate, $endDate, $pagePathFilter, $matchType) {
            $analyticsService = new GoogleAnalyticsService();

            $dimensionFilter = $pagePathFilter ? [
                'field_name' => 'pagePath',
                'match_type' => $matchType,
                'value' => $pagePathFilter,
                'case_sensitive' => true,
            ] : null;

            return $analyticsService->getAllPageAnalyticsData($startDate, $endDate, $dimensionFilter);
        });

        $report = $data['results'] ?? null;
        $totals = $data['totals'] ?? ['activeUsers' => 0, 'newUsers' => 0, 'totalUsers' => 0];
        if ($request->ajax() && !is_null($report)) {
            return view('track_pages.partials.analytics_table', compact('report', 'totals'))->render();
        }
        if ($request->ajax()) {
            return response()->json(['data' => 'false']);
        }

        return view('track_pages.list', compact('report', 'totals'));
    }
}
