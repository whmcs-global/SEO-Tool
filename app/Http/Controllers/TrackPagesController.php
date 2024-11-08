<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleAnalyticsService;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TrackPagesController extends Controller
{

    // public function list(Request $request)
    // {
    //     $analyticsService = new GoogleAnalyticsService();
    //     $startDate = 'yesterday';
    //     $endDate = 'yesterday';
    //     $data = $analyticsService->getAllPageAnalyticsData($startDate, $endDate);

    //     $report = $data['results'] ?? [];
    //     $totals = $data['totals'] ?? ['activeUsers' => 0, 'newUsers' => 0, 'totalUsers' => 0];

    //     return view('track_pages.list', compact('report', 'totals'));
    // }



    public function list(Request $request)
    {
        $daterange = $request->input('daterange');
        if ($daterange) {
            [$startDate, $endDate] = explode(' - ', $daterange);
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } else {
            $startDate = Carbon::yesterday()->format('Y-m-d');
            $endDate = $startDate;
        }

        $cacheKey = "analytics_data_{$startDate}_{$endDate}";
        $cacheDuration = 240;
        $data = Cache::remember($cacheKey, $cacheDuration, function () use ($startDate, $endDate) {
            $analyticsService = new GoogleAnalyticsService();
            return $analyticsService->getAllPageAnalyticsData($startDate, $endDate);
        });

        $report = $data['results'] ?? [];
        $totals = $data['totals'] ?? ['activeUsers' => 0, 'newUsers' => 0, 'totalUsers' => 0];

        return view('track_pages.list', compact('report', 'totals'));
    }
}
