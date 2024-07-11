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
    public function createToken($client, $clientId, $clientSecret, $redirectUrl, $refreshToken) {
        try {
            $response = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUrl,
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'scope' => 'https://www.googleapis.com/auth/webmasters https://www.googleapis.com/auth/webmasters.readonly https://www.googleapis.com/auth/userinfo.email',
                    // 'scope' => 'https://www.googleapis.com/auth/adwords',
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
