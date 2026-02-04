<?php

namespace App\Notifications\Channels;

use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FirebasePushChannel
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        // Get notification data
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        $data = $notification->toFirebase($notifiable);

        if (!$data) {
            Log::warning('Firebase notification data is empty');
            return;
        }

        // Get user's device tokens
        $tokens = $notifiable->deviceTokens()
            ->where('device_type', 'web')
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            Log::info('No device tokens found for user', ['user_id' => $notifiable->id]);
            return;
        }

        // Extract title and body
        $title = $data['title'] ?? 'New Notification';
        $body = $data['body'] ?? '';
        unset($data['title'], $data['body']);

        // Send notification
        $sent = $this->firebaseService->sendToMultipleDevices(
            $tokens,
            $title,
            $body,
            $data
        );

        if ($sent) {
            Log::info('Firebase push notification sent', [
                'user_id' => $notifiable->id,
                'tokens_count' => count($tokens),
                'title' => $title,
            ]);
        }
    }
}
