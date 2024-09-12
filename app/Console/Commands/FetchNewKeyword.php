<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\{Keyword, AdminSetting, Website, keyword_label, KeywordData};
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GzRequest;
use App\Traits\KeywordAnalytic;

class FetchNewKeyword extends Command
{
    use KeywordAnalytic;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'keyword:fetch-new-keyword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $website_ids = Website::pluck('id')->toArray();
        // $client = new Client();
        // foreach ($website_ids as $website_id) {
        //     $adminSetting = AdminSetting::where('website_id', $website_id)
        //         ->where('type', 'google')
        //         ->first();

        //     if (!is_null($adminSetting)) {
        //         $expiryTimeMinutes = $adminSetting->expiry_time;
        //         $pastUpdatedAccessTokenTime = Carbon::parse($adminSetting->created_at);
        //         $expirationTime = $pastUpdatedAccessTokenTime->copy()->addSeconds((int)$expiryTimeMinutes);

        //         $currentTime = Carbon::now();
        //         $accessToken = $adminSetting->access_token;
        //         if ($expirationTime->lessThan($currentTime) && ($adminSetting->status)) {
        //             $tokenResponse = $this->createToken(
        //                 $client,
        //                 jsdecode_userdata($adminSetting->client_id),
        //                 jsdecode_userdata($adminSetting->client_secret_id),
        //                 $adminSetting->redirect_url,
        //                 $adminSetting->refresh_token
        //             );
        //             if ($tokenResponse) {
        //                 $details = $tokenResponse;
        //                 $adminSetting->update([
        //                     'access_token' => $details['access_token'],
        //                     'expiry_time' => $details['expires_in'],
        //                     'created_at' => Carbon::now(),
        //                 ]);
        //                 $accessToken = $details['access_token'];
        //             }
        //         } else {
        //             $accessToken = $adminSetting->access_token;
        //         }

        //         $date = Carbon::now()->subDays(1)->format('Y-m-d');
        //         $analyticsData = $this->getNewKeywords($date, $client, $accessToken, $website_id);
        //         if (isset($analyticsData['code'])) {
        //             $this->info('Error in fetching data: ' . $analyticsData['message']);
        //             continue;
        //         }
        //         foreach ($analyticsData as $data) {
        //             $keyword = $data['keys'][0];
        //             $clicks = $data['clicks'];
        //             $impressions = $data['impressions'];
        //             $ctr = $data['ctr'];
        //             $position = $data['position'];

        //             $keywordData = Keyword::where('keyword', $keyword)
        //                 ->where('website_id', $website_id)
        //                 ->first();

        //             if (is_null($keywordData)) {
        //                 $DBkeyword = Keyword::create([
        //                     'keyword' => $keyword,
        //                     'website_id' => $website_id,
        //                 ]);

        //                 keyword_label::create([
        //                     'keyword_id' => $DBkeyword->id,
        //                     'label_id' => 11,
        //                 ]);
        //             }
        //         }
        //     }
        // }
        $this->createKeywordData();
        $this->info('Keyword metrics updated successfully.');
    }

    /**
     * Fetches the analytics data for the given date.
     *
     * @param string $date The date for which to fetch the data.
     * @param object $client The HTTP client instance.
     * @param string $accessToken The access token.
     * @param int $website_id The website ID.
     * @return array The analytics data.
     */
    function getNewKeywords($date, $client, $accessToken, $website_id)
    {
        try {
            // Constructing the query
            $query = [
                "dimensions" => [
                    "QUERY"
                ],
                "startDate" => $date,
                "endDate" => $date,
                "searchType" => 'web',
                "dataState" => "ALL",
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
            } else {
                $web_url = 'www.hostingseekers.com';
                $key = config('google.key');
                $property_type = 'url_prefix';
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
            return [
                'code' => $th->getCode(),
                'message' => $th->getMessage(),
            ];
        }
    }



    function analyticsQueryDatabyDate($startDate, $endDate, $client, $accessToken, $company, $type, $website_id, $code)
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

            // Setting headers
            $headers = [
                'Content-Type' => 'application/json'
            ];

            // Determining the URL, API key, and property type
            if ($website_id) {
                $website = Website::where('id', $website_id)->first();
                $web_url = $website->url;
                $key = $website->API_KEY;
                $property_type = $website->property_type;
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
    public function createToken($client, $clientId, $clientSecret, $redirectUrl, $refreshToken)
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
            $errorResponse = $e->getResponse()->getBody()->getContents();
            $errorData = json_decode($errorResponse, true);

            if (isset($errorData['error']) && $errorData['error'] === 'invalid_grant') {
                error_log('Refresh token has expired or been revoked.');
            } else {
                error_log('Error: ' . $e->getMessage());
            }

            return null;
        }
    }


    public function createKeywordData()
    {
        $keyword_no_data = Keyword::whereDoesntHave('keywordData')
            ->orWhereHas('keywordData', function ($query) {
                $query->whereNull('response');
            })
            ->get();

        $client = new Client();
        $website_ids = Website::pluck('id')->toArray();
        $countries = Country::all();
        $startDate = Carbon::now()->subDays(91)->format('Y-m-d');
        $endDate = Carbon::now()->subDays(1)->format('Y-m-d');
        foreach ($keyword_no_data as $keyword) {
            $website_id = $keyword->website_id;
            $adminSetting = AdminSetting::where('website_id', $website_id)
                ->where('type', 'google')
                ->first();
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
                        $adminSetting->refresh_token
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
                foreach ($countries as $country) {
                    $data = $this->keywords(request(), $keyword, $country->ISO_CODE);
                    $response = $this->analyticsQueryDatabyDate($startDate, $endDate, $client, $accessToken, $keyword->keyword, 'web', $website_id, $country->ISO_CODE);
                    if (isset($data[0]['code']) || isset($response['code'])) {
                        $this->info('Error in fetching data: ' . $data['message']);
                        continue;
                    }
                    KeywordData::create([
                        'keyword_id' => $keyword->id,
                        'country_id' => $country->id,
                        'position' => $data[0]['position'] ?? null,
                        'clicks' => $data[0]['clicks'] ?? null,
                        'impression' => $data[0]['impressions'] ?? null,
                        'response' => json_encode($response),
                    ]);
                }
            }
        }
    }
}
