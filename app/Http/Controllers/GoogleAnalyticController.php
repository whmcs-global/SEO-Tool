<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request as GzRequest;
use Auth, DateTime;
use App\Models\{AdminSetting, Keyword};
use Carbon\Carbon;
use App\Traits\{CreateGoogleToken};
use Exception;
use GuzzleHttp\Exception\RequestException;

class GoogleAnalyticController extends Controller
{
    use CreateGoogleToken;
    // Date: 30-04-2024

    public function redirectToGoogle(Request $request, Keyword $keyword)
    {
        try {
            $keyword_name = $keyword->keyword;
            // if(Auth::guard('fline')->user()->company_detail->company_name != $company) {
            //     return redirect()->back()->with('status', 'error')->with('message', 'Something went wrong');
            // }
            $dateFilter = $request->filled('date_filter') ? $request->date_filter : '';
            // $dateFilter = '01/01/2024 / 01/03/2024';
            $startDate = null;
            $endDate = null;

            // Parse the date range if dateFilter is not empty
            if ($dateFilter) {
                $dates = explode(' / ', $dateFilter);
                $startDate = Carbon::parse($dates[0])->format('Y-m-d');
                $endDate = Carbon::parse($dates[1])->format('Y-m-d');
            } else {
                $startDate = new DateTime();
                $startDate = $startDate->modify('-30 days')->format('Y-m-d');
                $end = new DateTime();
                $endDate = $end->format('Y-m-d');
            }
            $dateFilter= $startDate.' / '.$endDate;
            $client = new Client();
            $adminSetting = AdminSetting::first();
            // dd($adminSetting);
            $queryData = $dateData = [];
            if (!is_null($adminSetting)) {

                $expiryTimeMinutes = $adminSetting->expiry_time;
                $pastUpdatedAccessTokenTime = Carbon::parse($adminSetting->created_at);
                $expirationTime = $pastUpdatedAccessTokenTime->copy()->addSeconds((int)$expiryTimeMinutes);

                $currentTime = Carbon::now();
                $accessToken = $adminSetting->access_token;
                if ($expirationTime->lessThan($currentTime) && ($adminSetting->status)) {
                    $tokenResponse = $this->createToken($client, jsdecode_userdata($adminSetting->client_id), jsdecode_userdata($adminSetting->client_secret_id), $adminSetting->redirect_url, $adminSetting->refresh_token);
                    if ($tokenResponse) {
                        $details = $tokenResponse;
                        $adminSetting->update([
                            'access_token' => $details['access_token'],
                            'expiry_time' => $details['expires_in'],
                            'created_at' => Carbon::now(),
                        ]);
                        $accessToken =  $details['access_token'];
                    }
                } else {
                    $accessToken = $adminSetting->access_token;
                }

                if ($adminSetting->status) {
                    // dd($startDate, $endDate, $client, $accessToken, $keyword_name, $request->type);
                    $queryData = $this->analyticsQueryData($startDate, $endDate, $client, $accessToken, $keyword_name, $request->type ?? 'web');
                    $dateData = $this->analyticsChartData($startDate, $endDate, $client, $accessToken, $keyword_name, $request->type ?? 'web');
                    // dd($queryData, $dateData);
                    if (isset($queryData['code']) == 401) {

                        $errorMessage = 'Unable to fetch record. Error: Google console api token is not valid. Please contact administrator';
                        $queryData = $dateData = [];
                        return view('keyword.google-analytics', compact('queryData', 'dateData', 'dateFilter', 'errorMessage', 'keyword'));
                    }
                }
                return view('keyword.google-analytics', compact('queryData', 'dateData','keyword','dateFilter'));
            }

            $errorMessage = 'Unable to fetch record. Error: Google console api not configured. Please contact administrator';

            return view('keyword.google-analytics', compact('queryData', 'dateData', 'dateFilter', 'errorMessage', 'keyword'));
        } catch (RequestException $e) {
            return redirect()->route('dashboard')->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    function analyticsQueryData($startDate, $endDate, $client, $accessToken, $company, $type)
    {
        try {
            $Query = '{
                "dimensions": [
                    "QUERY"
                ],
                "startDate": "' . $startDate . '",
                "endDate": "' . $endDate . '",
                "dimensionFilterGroups": [
                    {
                        "filters": [
                            {
                                "operator": "CONTAINS",
                                "dimension": "QUERY",
                                "expression": "' . $company . '",
                            }
                        ]
                    }
                ],
                "searchType": "' . $type . '"
            }';
            $headers = [
                'Content-Type' => 'application/json'
            ];

            $request = new GzRequest('POST', 'https://searchconsole.googleapis.com/webmasters/v3/sites/https%3A%2F%2Fwww.hostingseekers.com%2F/searchAnalytics/query?key=AIzaSyAolG-tIIf72xBT3OQiYozPPbC2djfMj6w&access_token=' . $accessToken, $headers, $Query);
            $res = $client->sendAsync($request)->wait();
            $analyticsData = json_decode($res->getBody()->getContents()) ?? [];

            // Check if response status is successful
            if ($res->getStatusCode() != 200) {
                throw new Exception("Failed to fetch analytics data. Status Code: " . $res->getStatusCode());
            }

            // Check if response contains error message
            if (isset($analyticsData->error)) {
                throw new Exception("Error in fetching analytics data: " . $analyticsData->error->message);
            }

            $analyticsData = $analyticsData->rows ?? [];
            return $analyticsData;
        } catch (\Throwable $th) {
            return [
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
            ];
        }
    }

    function analyticsChartData($startDate, $endDate, $client, $accessToken, $company, $type)
    {
        try {
            $dateFilter = '{
                "dimensions": [
                    "DATE",
                ],
                "startDate": "' . $startDate . '",
                "endDate": "' . $endDate . '",
                "dimensionFilterGroups": [
                    {
                        "filters": [
                            {
                                "operator": "CONTAINS",
                                "dimension": "QUERY",
                                "expression": "' . $company . '"
                            }
                        ]
                    }
                ],
                "searchType": "' . $type . '"
            }';

            $headers = [
                'Content-Type' => 'application/json'
            ];


            $request = new GzRequest('POST', 'https://searchconsole.googleapis.com/webmasters/v3/sites/https%3A%2F%2Fwww.hostingseekers.com%2F/searchAnalytics/query?key=AIzaSyAolG-tIIf72xBT3OQiYozPPbC2djfMj6w&access_token=' . $accessToken, $headers, $dateFilter);
            $res = $client->sendAsync($request)->wait();
            $analyticsData = json_decode($res->getBody()->getContents()) ?? [];
            // Check if response status is successful
            if ($res->getStatusCode() != 200) {
                throw new Exception("Failed to fetch analytics data. Status Code: " . $res->getStatusCode());
            }

            // Check if response contains error message
            if (isset($analyticsData->error)) {
                throw new Exception("Error in fetching analytics data: " . $analyticsData->error->message);
            }

            $analyticsData = $analyticsData->rows ?? [];
            return $analyticsData;
        } catch (\Throwable $th) {
            return [
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
            ];
        }
    }

}
