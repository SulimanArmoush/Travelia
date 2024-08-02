<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

trait NotificationTrait
{
    public function send($deviceToken,$title,$body): string
    {
        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);

        $messaging->send($message);

        return 'Notification sent';
    }
}
