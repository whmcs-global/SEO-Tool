<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\{Keyword, AdminSetting, Website};
use Auth, DateTime;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GzRequest;

trait KeywordAnalytic
{
    public function keywords(Request $request, Keyword $keyword)
    {
        try {
            $keyword_name = $keyword->keyword;
            $dateFilter = $request->filled('date_filter') ? $request->date_filter : '';
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

            $adminSetting = AdminSetting::where('website_id', auth()->user()->website_id)->first();
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
                    $queryData = $this->analyticsQueryData($startDate, $endDate, $client, $accessToken, $keyword_name, $request->type ?? 'web');
                }
                return $queryData;
            } else {
                return redirect()->route('dashboard')->with('status', 'error')->with('message', 'Admin setting not found');
            }
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
                                "operator": "EQUALS",
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
                
            if(auth()->user()->website_id){
                $website = Website::where('id', auth()->user()->website_id)->first();
                $web_url = $website->url;
                $key = $website->API_KEY;
            }else{
                $web_url = 'www.hostingseekers.com';
                $key = config('google.key');
            }
            $request = new GzRequest('POST', 'https://searchconsole.googleapis.com/webmasters/v3/sites/https%3A%2F%2F'.$web_url.'%2F/searchAnalytics/query?key='.$key.'&access_token=' . $accessToken, $headers, $Query);
            $res = $client->sendAsync($request)->wait();
            $analyticsData = json_decode($res->getBody()->getContents()) ?? [];

            if ($res->getStatusCode() != 200) {
                throw new Exception("Failed to fetch analytics data. Status Code: " . $res->getStatusCode());
            }

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

    public function createToken($client, $clientId, $clientSecret, $redirectUrl, $refreshToken) {
        try {
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUrl, // Use the provided redirect URL
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'scope' => 'https://www.googleapis.com/auth/webmasters https://www.googleapis.com/auth/webmasters.readonly https://www.googleapis.com/auth/userinfo.email',
                ],
            ]);

            $body = $response->getBody();
            $data = json_decode($body, true);

            if (isset($data['access_token'])) {
                return $data;
            } else {
                throw new Exception('Error obtaining access token');
            }
        } catch (Exception $e) {
            $errorResponse = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($errorResponse, true);

            if (isset($errorData['error']) && $errorData['error'] === 'invalid_grant') {
                // Handle specific case where the refresh token is expired or revoked
                error_log('Refresh token has expired or been revoked.');
                // Notify the system/user to re-authenticate
            } else {
                // Log other errors
                error_log('Error: ' . $e->getMessage());
            }

            return null;
        }
    }
}
