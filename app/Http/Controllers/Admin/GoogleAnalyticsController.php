<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\AdminSetting;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use App\Traits\{CreateGoogleToken};
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\Models\Website;

class GoogleAnalyticsController extends Controller
{
    use CreateGoogleToken;
     /**
     * Sign in with google account.
     */
    public function configGoogleAnalytics(Request $request)
    {
        $adminSetting = AdminSetting::where('website_id', auth()->user()->website_id)->where('type','google')->first(); //where('user_id', auth()->id())->
        // dd($adminSetting);
        if(!is_null($adminSetting)) {
            // Initialize Guzzle client
            $client = new Client();
            $expiryTimeMinutes = Carbon::parse($adminSetting->expiry_time);
            $pastUpdatedAccessTokenTime = Carbon::parse($adminSetting->created_at);
            $expirationTime = $pastUpdatedAccessTokenTime->copy()->addSeconds($expiryTimeMinutes);
            $currentTime = Carbon::now();
            if($expirationTime->lessThan($currentTime) && ($adminSetting->status)) {
                $tokenResponse = $this->createToken($client, jsdecode_userdata($adminSetting->client_id), jsdecode_userdata($adminSetting->client_secret_id), $adminSetting->redirect_url, $adminSetting->refresh_token);
                if($tokenResponse) {
                    $details = $tokenResponse;
                    $adminSetting->update([
                        'access_token' => $details['access_token'],
                        'expiry_time' => $details['expires_in'],
                        'created_at' => Carbon::now(),
                    ]);
                }
            }
        }

        $googleads = AdminSetting::where('website_id', auth()->user()->website_id)->where('type','google_ads')->first(); //where('user_id', auth()->id())->
        if(!is_null($googleads)) {
            $client = new Client();
            $expiryTimeMinutes = Carbon::parse($googleads->expiry_time);
            $pastUpdatedAccessTokenTime = Carbon::parse($googleads->created_at);
            $expirationTime = $pastUpdatedAccessTokenTime->copy()->addSeconds($expiryTimeMinutes);
            $currentTime = Carbon::now();
            if($expirationTime->lessThan($currentTime) && ($googleads->status)) {
                $tokenResponse = $this->createToken($client, jsdecode_userdata($googleads->client_id), jsdecode_userdata($googleads->client_secret_id), $googleads->redirect_url, $googleads->refresh_token);
                if($tokenResponse) {
                    $details = $tokenResponse;
                    $googleads->update([
                        'access_token' => $details['access_token'],
                        'expiry_time' => $details['expires_in'],
                        'created_at' => Carbon::now(),
                    ]);
                }
            }
        }

        return view('settings.index', compact('adminSetting', 'googleads'));
    }

    public function googleConnect()
    {
        try {
            // Replace these values with your Google OAuth credentials
            if(!auth()->user()->website_id){
                // $clientID = config('google.client_id');
                // $redirectUri = config('google.redirect_url');
                return redirect()->back()->with('error', 'Please add website first.');
            }
            else {
                $web_id = auth()->user()->website_id;
                $website = Website::where('id', $web_id)->first();
                $clientID = $website->GOOGLE_ANALYTICS_CLIENT_ID;
                $redirectUri = $website->GOOGLE_ANALYTICS_REDIRECT_URI;
            }

            $scope = urlencode('https://www.googleapis.com/auth/webmasters https://www.googleapis.com/auth/webmasters.readonly https://www.googleapis.com/auth/userinfo.email');
            // dd($clientID, $redirectUri, $scope);
            // Construct the URL
            $auth_url = "https://accounts.google.com/o/oauth2/v2/auth?redirect_uri={$redirectUri}&prompt=consent&response_type=code&client_id={$clientID}&scope={$scope}&access_type=offline";

            return redirect()->to($auth_url);
        } catch (\Exception $e) {
            Log::error('Error in googleConnect: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while trying to connect to Google.');
        }
    }

    public function callbackToGoogle(Request $request)
    {
       // Replace these values with your Google OAuth credentials
    if(!auth()->user()->website_id){
        $clientID = config('google.client_id');
        $clientSecret = config('google.client_secret');
        $redirectUri = config('google.redirect_url');
    }
    else {
        $web_id = auth()->user()->website_id;
        $website = Website::where('id', $web_id)->first();
        $clientSecret = $website->GOOGLE_ANALYTICS_CLIENT_SECRET;
        $clientID = $website->GOOGLE_ANALYTICS_CLIENT_ID;
        $redirectUri = $website->GOOGLE_ANALYTICS_REDIRECT_URI;
    }
       try {
            // Initialize Guzzle client
            $client = new Client();
            // Construct the URL for token exchange
            $token_url = 'https://oauth2.googleapis.com/token';
            if(!is_null($request->code)) {
                // Make the request
                $response = $client->post($token_url, [
                    'form_params' => [
                        'code' => $request->code,
                        'client_id' => $clientID,
                        'client_secret' => $clientSecret,
                        'redirect_uri' => $redirectUri,
                        'grant_type' => 'authorization_code'
                    ]
                ]);
                // Get the response body
                $body = $response->getBody()->getContents();

                // Decode the JSON response
                $token_data = json_decode($body, true);
                // Access token and other details
                $access_token = $token_data['access_token'];
                if($access_token) {
                    $adminSetting = AdminSetting::create([
                        'user_id' => auth()->id(),
                        'website_id' => auth()->user()->website_id,
                        'type' => 'google',
                        'client_id' => jsencode_userdata($clientID),
                        'client_secret_id' => jsencode_userdata($clientSecret),
                        'redirect_url' => config('google.redirect_url'),
                        'access_token'=> $access_token,
                        'refresh_token'=> $token_data['refresh_token'],
                        'expiry_time'=> $token_data['expires_in'],
                        'status' => 1,
                    ]);
                    $googleads = AdminSetting::where('website_id', auth()->user()->website_id)->where('type','google_ads')->first();
                    $closeWindow = 'window-hide';
                    return view('settings.index', compact('adminSetting', 'closeWindow'));
                }
            }

            return redirect()->route('dashboard')->with('status', 'error')->with('message', 'Something went wrong.');

        } catch (RequestException $e) {

            return redirect()->route('dashboard')->with('status', 'error')->with('message', $e->getMessage());
        }
    }

    public function changeStatus(AdminSetting $adminSetting)
    {
        $client = new Client();

        $endpoint = 'https://oauth2.googleapis.com/revoke';
        try {
            // Send a POST request to revoke the token
            $response = $client->post($endpoint, [
            'form_params' => [
                'token' => $adminSetting->access_token
                ]
            ]);
            // Check if the token was successfully revoked
            if ($response->getStatusCode() === 200) {
                $adminSetting->delete();
                echo "Access token revoked successfully\n";
            } else {
                echo "Error revoking access token\n";
            }

            return redirect()->back()->with('status', 'success')->with('message', 'Google api disconnected successfully.');
        } catch (\Throwable $th) {
            Log::error('Error in changeStatus: ' . $th->getMessage());
            return redirect()->route('dashboard')->with('status', 'error')->with('message', 'Something Went Wrong');
        }
    }
}
