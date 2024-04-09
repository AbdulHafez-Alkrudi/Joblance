<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    public function myNotifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications;

        if (empty($notifications))
        {
            return $this->sendResponse([]);
        }

        foreach($notifications as $key => &$notification)
        {
            $response = $this->show($notification);
            if ($response->getData()->status == 'failure')
            {
                return $response;
            }
            $notifications[$key] = $response->getData()->data;
        }

        $notifications = $notifications->sortByDesc('date');
        $notifications = array_values($notifications->all());

        return $this->sendResponse($notifications);
    }

    public function newNotifications()
    {
        $user = Auth::user();

        $notifications = $user->unreadNotifications;

        foreach ($notifications as $key => &$notification)
        {
            $notification->markAsRead();
            $response = $this->show($notification);

            if ($response->getData()->status == 'failure')
            {
                return $response;
            }

            $notifications[$key] = $response->getData()->data;
            $notification->markAsRead();
        }

        $notifications = $notifications->sortByDesc('date');
        $notifications = array_values($notifications->all());

        return $this->sendResponse($notifications);
    }

    public function show($notification)
    {
        if (is_null($notification))
        {
            return $this->sendError(["error" => "this notification isn't found"]);
        }

        $notification_data = [
            'id' => $notification->id,
            'title' => $notification['data']['title'],
            'body'  => $notification['data']['body'],
            'is_read' => ($notification->read_at == null) ? false : true,
            'date' => $notification->created_at,
        ];

        return $this->sendResponse($notification_data);
    }
}
