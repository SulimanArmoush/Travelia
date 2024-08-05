<?php

namespace App\Traits;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

trait NotificationTrait
{
    public function send($deviceToken, $title, $body): void
    {
        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        //$messaging->send($message);
    }
}
