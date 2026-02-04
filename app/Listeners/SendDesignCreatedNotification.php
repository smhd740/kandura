<?php

namespace App\Listeners;

use App\Events\DesignCreated;
use App\Models\User;
use App\Notifications\DesignCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDesignCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(DesignCreated $event): void
{
    $admins = User::whereHas('roles', function ($q) {
        $q->whereIn('name', ['admin', 'super_admin']);
    })->get();

    foreach ($admins as $admin) {
        if ($admin instanceof User) {
            $admin->notify(new DesignCreatedNotification($event->design));
        }
    }
}
}
