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
