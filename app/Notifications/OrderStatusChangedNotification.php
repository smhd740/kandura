<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Channels\FirebasePushChannel;

class OrderStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(Order $order, $oldStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $order->status;
    }

    public function via(object $notifiable): array
{
    return ['database', FirebasePushChannel::class];
}

    public function toArray(object $notifiable): array
    {
        $statusMessages = [
            'pending' => [
                'title' => 'Order Created',
                'body' => "Your order #{$this->order->order_number} has been placed successfully.",
                'action' => 'view order details',
            ],
            'processing' => [
                'title' => 'Order Processing',
                'body' => "Your order #{$this->order->order_number} is now being processed.",
                'action' => 'view order details',
            ],
            'completed' => [
                'title' => 'Order Completed',
                'body' => "Your order #{$this->order->order_number} has been completed successfully.",
                'action' => 'view order details',
            ],
            'cancelled' => [
                'title' => 'Order Cancelled',
                'body' => "Your order #{$this->order->order_number} has been cancelled.",
                'action' => 'view order details',
            ],
        ];

        $message = $statusMessages[$this->newStatus] ?? [
            'title' => 'Order Status Updated',
            'body' => "The status of your order #{$this->order->order_number} has been updated to {$this->newStatus}.",
            'action' => 'view order details',
        ];

        return [
            'event' => 'order status changed',
            'receivers' => 'user',
            'title' => $message['title'],
            'body' => $message['body'],
            'action' => $message['action'],
            'methods' => 'DB',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }

    /**
 * Get the Firebase representation of the notification.
 */
public function toFirebase(object $notifiable): array
{
    $statusMessages = [
        'pending' => [
            'title' => 'Order Created',
            'body' => "Your order #{$this->order->order_number} has been placed successfully.",
        ],
        'processing' => [
            'title' => 'Order Processing',
            'body' => "Your order #{$this->order->order_number} is now being processed.",
        ],
        'completed' => [
            'title' => 'Order Completed',
            'body' => "Your order #{$this->order->order_number} has been completed successfully.",
        ],
        'cancelled' => [
            'title' => 'Order Cancelled',
            'body' => "Your order #{$this->order->order_number} has been cancelled.",
        ],
    ];

    $message = $statusMessages[$this->newStatus] ?? [
        'title' => 'Order Status Updated',
        'body' => "The status of your order #{$this->order->order_number} has been updated.",
    ];

    return [
        'title' => $message['title'],
        'body' => $message['body'],
        'order_id' => $this->order->id,
        'order_number' => $this->order->order_number,
        'type' => 'order_status_changed',
    ];
}


}
