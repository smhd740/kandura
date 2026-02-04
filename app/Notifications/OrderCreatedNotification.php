<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Channels\FirebasePushChannel;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $isForAdmin;

    public function __construct(Order $order, bool $isForAdmin = false)
    {
        $this->order = $order;
        $this->isForAdmin = $isForAdmin;
    }

    public function via(object $notifiable): array
{
    return ['database', FirebasePushChannel::class];
}

    public function toArray(object $notifiable): array
    {
        if ($this->isForAdmin) {
            return [
                'event' => 'order created',
                'receivers' => 'admin',
                'title' => 'New Order Created',
                'body' => "A new order #{$this->order->order_number} has been placed by {$this->order->user->name}.",
                'action' => 'go to order list',
                'methods' => 'DB / Push notifications',
                'order_id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'user_id' => $this->order->user_id,
                'user_name' => $this->order->user->name,
                'total_amount' => $this->order->total_amount,
            ];
        }

        return [
            'event' => 'order created',
            'receivers' => 'user who create design',
            'title' => 'New Order Created',
            'body' => "A new order has been placed for your design. Tap to view the order details.",
            'action' => 'go to order list',
            'methods' => 'DB / Push notifications',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
        ];
    }

    /**
 * Get the Firebase representation of the notification.
 */
/**
 * Get the Firebase representation of the notification.
 */
public function toFirebase(object $notifiable): array
{
    if ($this->isForAdmin) {
        return [
            'title' => 'New Order Created',
            'body' => "A new order #{$this->order->order_number} has been placed by {$this->order->user->name}.",
            'order_id' => (string) $this->order->id,
            'order_number' => (string) $this->order->order_number,
            'type' => 'new_order_admin',
        ];
    }

    return [
        'title' => 'New Order Created',
        'body' => "A new order has been placed for your design. Tap to view the order details.",
        'order_id' => (string) $this->order->id,
        'order_number' => (string) $this->order->order_number,
        'type' => 'new_order_user',
    ];
}
}

