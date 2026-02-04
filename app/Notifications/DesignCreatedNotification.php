<?php

namespace App\Notifications;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Channels\FirebasePushChannel;

class DesignCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $design;

    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function via(object $notifiable): array
{
    return ['database', FirebasePushChannel::class];
}

    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'design created',
            'receivers' => 'admin',
            'title' => 'New Design Created',
            'body' => "A new design has been created by a user. Tap to review it in the design list",
            'action' => 'go to design list',
            'methods' => 'DB / Push notifications',
            'design_id' => $this->design->id,
            'design_name' => $this->design->name,
            'user_id' => $this->design->user_id,
            'user_name' => $this->design->user->name,
        ];
    }

    /**
 * Get the Firebase representation of the notification.
 */
public function toFirebase(object $notifiable): array
{
    return [
        'title' => 'New Design Created',
        'body' => "A new design has been created by {$this->design->user->name}. Tap to review it.",
        'design_id' => (string) $this->design->id,
        'design_name' => (string) $this->design->name,
        'type' => 'design_created',
    ];
}
}
