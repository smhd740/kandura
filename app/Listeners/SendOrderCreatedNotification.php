<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\User;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

   public function handle(OrderCreated $event): void
{
    $order = $event->order;

    // 1. إرسال للـ admins
    $admins = User::whereHas('roles', function ($q) {
        $q->whereIn('name', ['admin', 'super_admin']);
    })->get();

    foreach ($admins as $admin) {
        if ($admin instanceof User) {
            $admin->notify(new OrderCreatedNotification($order, true));
        }
    }

    // 2. إرسال لأصحاب التصاميم (فقط إذا في items)
    if ($order->items()->exists()) {
        $designOwnerIds = $order->items()
            ->with('design.user')
            ->get()
            ->pluck('design.user_id')
            ->unique()
            ->filter(function ($userId) use ($order) {
                return $userId && $userId !== $order->user_id;
            });

        if ($designOwnerIds->isNotEmpty()) {
            $designOwners = User::whereIn('id', $designOwnerIds)->get();

            foreach ($designOwners as $owner) {
                if ($owner instanceof User) {
                    $owner->notify(new OrderCreatedNotification($order, false));
                }
            }
        }
    }
}
}
