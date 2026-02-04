<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirebaseService
{
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = 'kandura-store-notifications';
        $this->accessToken = $this->getAccessToken();
    }

    protected function getAccessToken()
    {
        try {
            $credentialsPath = storage_path('app/firebase/service-account.json');

            if (!file_exists($credentialsPath)) {
                Log::error('Firebase credentials file not found');
                return null;
            }

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($credentialsPath), true)
            );

            $token = $credentials->fetchAuthToken();
            return $token['access_token'] ?? null;

        } catch (Exception $e) {
            Log::error('Failed to get Firebase access token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        return $this->send([$deviceToken], $title, $body, $data);
    }

    public function sendToMultipleDevices(array $deviceTokens, string $title, string $body, array $data = []): bool
    {
        if (empty($deviceTokens)) {
            return false;
        }
        return $this->send($deviceTokens, $title, $body, $data);
    }

    protected function send(array $tokens, string $title, string $body, array $data = []): bool
    {
        if (!$this->accessToken) {
            Log::error('No Firebase access token available');
            return false;
        }

        try {
            $success = 0;

            foreach ($tokens as $token) {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data,
                        'webpush' => [
                            'notification' => [
                                'icon' => asset('images/notification-icon.png'),
                            ],
                        ],
                    ],
                ]);

                if ($response->successful()) {
                    $success++;
                } else {
                    Log::error('Firebase send failed', [
                        'token' => $token,
                        'response' => $response->body(),
                    ]);
                }
            }

            return $success > 0;

        } catch (Exception $e) {
            Log::error('Firebase notification exception', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
