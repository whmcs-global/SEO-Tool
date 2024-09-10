<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\{Keyword, AdminSetting, Website, keyword_label};
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as GzRequest;


class FetchNewKeyword extends Command
{
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
        $website_ids = Website::pluck('id')->toArray();
        $countries = Country::pluck('ISO_CODE')->toArray();
        $client = new Client();
        foreach ($website_ids as $website_id) {
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

                $date = Carbon::now()->subDays(1)->format('Y-m-d');
                $analyticsData = $this->analyticsQueryDatabyDate($date, $client, $accessToken, $website_id);
                if (isset($analyticsData['code'])) {
                    $this->info('Error in fetching data: ' . $analyticsData['message']);
                    continue;
                }
                foreach ($analyticsData as $data) {
                    $keyword = $data['keys'][0];
                    $clicks = $data['clicks'];
                    $impressions = $data['impressions'];
                    $ctr = $data['ctr'];
                    $position = $data['position'];

                    $keywordData = Keyword::where('keyword', $keyword)
                        ->where('website_id', $website_id)
                        ->first();

                    if (is_null($keywordData)) {
                        $DBkeyword =Keyword::create([
                            'keyword' => $keyword,
                            'website_id' => $website_id,
                        ]);

                        keyword_label::create([
                            'keyword_id' => $DBkeyword->id,
                            'label_id' => 11,
                        ]);
                    }
                }
            }
        }

        $this->info('Keyword metrics updated successfully.');
    }

    function analyticsQueryDatabyDate($date, $client, $accessToken, $website_id)
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
}
