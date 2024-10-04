<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\{Keyword, AdminSetting, Website, Website_last_updated};
use Auth, DateTime;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GzRequest;
use Illuminate\Support\Facades\Log;
use App\Services\ExternalApiLogger;


trait KeywordDaterange
{
    /**
     * Fetches keyword analytics data based on date range and other parameters.
     *
     * @param Keyword $keyword The keyword model instance.
     * @param string $code The countey code by google for the query.
     * @param  int The cron id.
     * @param string|null $startDate The start date of the date range.
     * @param string|null $endDate The end date of the date range.
     * @return array The keyword analytics data fetched from the API.
     */
    public function keywordbydate(Keyword $keyword, $code, $cron_id, $startDate = null, $endDate = null)
    {
        try {
            $keyword_name = $keyword->keyword;
            if (is_null($startDate) || is_null($endDate)) {
                $startDate = Carbon::now()->subDays(91)->format('Y-m-d');
                $endDate = Carbon::now()->subDays(1)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            }

            $dateFilter = $startDate . ' / ' . $endDate;
            $client = new Client();

            $adminSetting = AdminSetting::where('website_id', $keyword->website_id)
                ->where('type', 'google')
                ->first();

            $queryData = $dateData = [];
            if (!is_null($adminSetting)) {
                $expiryTimeMinutes = $adminSetting->expiry_time;
                $pastUpdatedAccessTokenTime = Carbon::parse($adminSetting->created_at);
                $expirationTime = $pastUpdatedAccessTokenTime->copy()->addSeconds((int)$expiryTimeMinutes);

                $currentTime = Carbon::now();
                $accessToken = $adminSetting->access_token;
                if ($expirationTime->lessThan($currentTime) && ($adminSetting->status)) {
                    $tokenResponse = $this->createToken(
                        $client,
                        jsdecode_userdata($adminSetting->client_id),
                        jsdecode_userdata($adminSetting->client_secret_id),
                        $adminSetting->redirect_url,
                        $adminSetting->refresh_token,
                        $cron_id
                    );
                    if ($tokenResponse) {
                        $details = $tokenResponse;
                        $adminSetting->update([
                            'access_token' => $details['access_token'],
                            'expiry_time' => $details['expires_in'],
                            'created_at' => Carbon::now(),
                        ]);
                        $accessToken = $details['access_token'];
                    }
                } else {
                    $accessToken = $adminSetting->access_token;
                }

                if ($adminSetting->status) {
                    $queryData = $this->analyticsQueryDatabyDate(
                        $startDate,
                        $endDate,
                        $client,
                        $accessToken,
                        $keyword_name,
                        'WEB',
                        $keyword->website_id,
                        $code,
                        $cron_id
                    );
                }
                return $queryData;
            } else {
                return [
                    'code' => 401,
                    'message' => 'Google API settings not found',
                ];
            }
        } catch (RequestException $e) {

            ExternalApiLogger::log(
                $cron_id,
                'Google Analytics',
                $e->getMessage(),
                'API Endpoint (e.g., URL)',
                'POST',
                json_encode($keyword),
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
                $e->getCode()
            );

            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }
    }


    /**
     * Queries the Google Analytics API to fetch analytics data based on date range and other parameters.
     *
     * @param string $startDate The start date of the date range.
     * @param string $endDate The end date of the date range.
     * @param object $client The GuzzleHttp client instance.
     * @param string $accessToken The access token for authentication.
     * @param string $company The company name for filtering the data.
     * @param string $type The search type for the query.
     * @param int $website_id The ID of the website.
     * @param string $code The code for the query.
     * @return array The analytics data fetched from the API.
     */
    function analyticsQueryDatabyDate($startDate, $endDate, $client, $accessToken, $company, $type, $website_id, $code, $cron_id)
    {
        try {
            // Constructing the query
            $query = [
                "dimensions" => [
                    "QUERY",
                    "DATE"
                ],
                "startDate" => $startDate,
                "endDate" => $endDate,
                "dimensionFilterGroups" => [
                    [
                        "filters" => [
                            [
                                "operator" => "EQUALS",
                                "dimension" => "QUERY",
                                "expression" => $company
                            ],
                            [
                                "operator" => "CONTAINS",
                                "dimension" => "COUNTRY",
                                "expression" => $code
                            ]
                        ]
                    ]
                ],
                "searchType" => $type,
                "dataState" => "ALL"
            ];
            $jsonQuery = json_encode($query);

            $headers = [
                'Content-Type' => 'application/json'
            ];

            if ($website_id) {
                $website = Website::where('id', $website_id)->first();
                $web_url = $website->url;
                $key = $website->API_KEY;
                $property_type = $website->property_type;
                Website_last_updated::where('website_id', $website_id)
                ->update([
                    'last_updated_at' => Carbon::now(),
                ]);
            } else {
                $web_url = 'www.hostingseekers.com';
                $key = config('google.key');
                $property_type = 'url_prefix'; // Default to 'url_prefix' if no website_id
            }

            // Handling property type
            if ($property_type == 'url_prefix') {
                $encoded_url = urlencode($web_url);
            } else if ($property_type == 'domain') {
                $encoded_url = 'sc-domain:' . $web_url;
            } else {
                throw new Exception("Invalid property type: " . $property_type);
            }

            // Constructing the request URL
            $requestUrl = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . $encoded_url . '/searchAnalytics/query?key=' . $key . '&access_token=' . $accessToken;

            // Constructing the request
            $request = new GzRequest('POST', $requestUrl, $headers, $jsonQuery);

            // Sending the request
            $res = $client->sendAsync($request)->wait();
            $analyticsData = json_decode($res->getBody()->getContents(), true);

            // Error handling
            if ($res->getStatusCode() != 200) {
                throw new Exception("Failed to fetch analytics data. Status Code: " . $res->getStatusCode());
            }

            if (isset($analyticsData['error'])) {
                throw new Exception("Error in fetching analytics data: " . $analyticsData['error']['message']);
            }

            return $analyticsData['rows'] ?? [];
        } catch (\Throwable $th) {
            ExternalApiLogger::log(
                $cron_id,
                'Google Search Console',
                $th->getMessage(),
                $requestUrl,
                'POST',
                json_encode($query),
                $th->getMessage(),
                $th->getCode()
            );
            return [
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
            ];
        }
    }


    /**
     * Creates an access token using the provided credentials and refresh token.
     *
     * @param object $client The HTTP client instance.
     * @param string $clientId The client ID.
     * @param string $clientSecret The client secret.
     * @param string $redirectUrl The redirect URL.
     * @param string $refreshToken The refresh token.
     * @return array|null The access token data if successful, null otherwise.
     * @throws Exception If there is an error obtaining the access token.
     */
    public function createToken($client, $clientId, $clientSecret, $redirectUrl, $refreshToken, $cron_id)
    {
        try {
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUrl,
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
            ExternalApiLogger::log(
                $this->cron,
                'Google OAuth Token Request',
                $e->getMessage(),
                'https://oauth2.googleapis.com/token',
                'POST',
                json_encode([
                    'client_id' => $clientId,
                    'client_secret' => '****',
                    'redirect_uri' => $redirectUrl,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => '****',
                ]),
                $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null,
                $e->getCode()
            );

            $errorResponse = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null;
            $errorData = json_decode($errorResponse, true);

            if (isset($errorData['error']) && $errorData['error'] === 'invalid_grant') {
                error_log('Refresh token has expired or been revoked.');
            } else {
                error_log('Error: ' . $e->getMessage());
            }

            return null;
        }
    }

}
