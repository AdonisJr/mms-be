<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Storage;

class FirebaseService
{
    protected $client;

    public function __construct()
    {
        // Initialize Google Client
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app/firebase/service-account.json')); // Path to your service account JSON file
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Log initialization
        Log::info('FirebaseService initialized');
    }

    /**
     * Get the Firebase access token
     *
     * @return string
     * @throws \Exception
     */
    public function getAccessToken()
    {
        // Authenticate using the service account credentials
        try {
            if ($this->client->isAccessTokenExpired()) {
                Log::info('Access token expired, fetching a new token');
                $this->client->fetchAccessTokenWithAssertion();
            }

            // Get the access token
            $accessToken = $this->client->getAccessToken();

            if (isset($accessToken['access_token'])) {
                Log::info('Access token retrieved successfully');
                return $accessToken['access_token'];
            }

            throw new \Exception('Unable to get access token.');
        } catch (\Exception $e) {
            Log::error('Error getting access token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send a push notification via Firebase Cloud Messaging
     *
     * @param string $token
     * @param string $title
     * @param string $message
     */
    public function sendNotification($token, $title, $message)
    {
        try {
            // Get the access token
            $accessToken = $this->getAccessToken();

            // Prepare the notification payload
            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'data' => [
                        'customData' => 'value', // Add any custom data here
                    ],
                ],
            ];

            // Log the payload for debugging
            Log::info('Sending notification', ['payload' => $payload]);

            // Send the notification using Firebase FCM
            $url = 'https://fcm.googleapis.com/v1/projects/gsmms-web/messages:send';

            $response = $this->makePostRequest($url, $payload, $accessToken);

            Log::info('Notification sent successfully', ['response' => $response]);
            
            return $response;
        } catch (\Exception $e) {
            Log::error('Error sending notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Make POST request
     *
     * @param string $url
     * @param array $payload
     * @param string $accessToken
     * @return mixed
     */
    private function makePostRequest($url, $payload, $accessToken)
    {
        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \Exception('cURL Error: ' . curl_error($ch));
            }

            curl_close($ch);

            // Log response
            Log::info('cURL response', ['response' => $response]);

            return json_decode($response, true);
        } catch (\Exception $e) {
            Log::error('Error in cURL request: ' . $e->getMessage());
            throw $e;
        }
    }
}
