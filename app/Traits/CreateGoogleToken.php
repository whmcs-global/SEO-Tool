<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Exception;

trait CreateGoogleToken
{
    /**
     * Create a new access token for Google API.
     *
     * @param Client $client
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     * @param string $refreshToken
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    // public function createToken($client, $clientId, $clientSecret, $redirectUrl, $refreshToken)
    // {
    //     try {
    //         $tokenUrl = 'https://oauth2.googleapis.com/token';
    //         $body = [
    //             'client_id' => $clientId,
    //             'client_secret' => $clientSecret,
    //             'redirect_uri' => 'https://dev.hostingseekers.com/hsconsole/google/callback',
    //             'refresh_token' => 'ya29.a0AXooCgv12dpkKuZviyOdUzBdLTDFZeTKhIV4N2s9Yb_mjj9kAi894nRzSyZaXdFiNuK2SkOBArY-tYrOacsb1gsKNhXMYIjlv9bhW5WY48KKbA7cX3HGRAyblAtjaZxhs_PDuky1WWEMRWGx4munRVH3jzLtPWcRjaQaCgYKAegSARMSFQHGX2MiH_g1HuXkZEeuBN4C2LLzhg0170',
    //             'grant_type' => 'refresh_token',
    //         ];
    //         // dd($body);
    //         $request = new Request('POST', $tokenUrl, ['form_params' => $body]);
    //         // dd($request);
    //         $response = $client->send($request);
    //         dd($response);
    //         return $response;
    //     } catch (Exception $e) {
    //         dd($e->getMessage());
    //         return null;
    //     }
    // }
    public function createToken ($client, $clientId, $clientSecret, $redirectUrl, $refreshToken) {
        try {
            $client = new Client();
            $response = $client->post ( 'https://oauth2.googleapis.com/token', [
            'form_params' => [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => 'https://dev.hostingseekers.com/google/callback',
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'scope' => 'https://www.googleapis.com/auth/webmasters https://www.googleapis.com/auth/webmasters.readonly https://www.googleapis.com/auth/userinfo.email',
            ],
            ]);
            $body = $response->getBody();
            return $body;
        }
        catch (Exception $e) {
            return null;
        }
    }
}
