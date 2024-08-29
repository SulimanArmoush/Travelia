<?php

namespace App\Traits;

use App\Models\Not;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

trait NotificationTrait
{
    public function send($user, $title, $body): void
    {

        $deviceToken = $user->deviceToken;

        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);

        //$notification = $notification->withImageUrl('');

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification);


        $messaging->send($message);

        Not::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);
    }
}
