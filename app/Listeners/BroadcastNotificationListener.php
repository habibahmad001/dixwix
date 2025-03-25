<?php

namespace App\Listeners;

use App\Events\NotificationEvent;
use Illuminate\Notifications\Events\NotificationSent;

class BroadcastNotificationListener
{
    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if ($event->notification instanceof \App\Notifications\GeneralNotification) {
            $notifiable = $event->notifiable;
            $notification = $notifiable->notifications()->latest()->first();

            event(new NotificationEvent($notification, $notifiable->id));
        }
    }
}
