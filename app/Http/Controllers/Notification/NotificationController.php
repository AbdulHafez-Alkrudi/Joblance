<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;

class NotificationController extends BaseController
{
    public function myNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications;
        if ($notifications->isEmpty())
        {
            return $this->sendResponse([]);
        }

        foreach($notifications as $key => &$notification)
        {
            $notifications[$key] = $this->show($notification);
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
            $notifications[$key] = $this->show($notification);
            $notification->markAsRead();
        }

        $notifications = $notifications->sortByDesc('date');
        $notifications = array_values($notifications->all());

        return $this->sendResponse($notifications);
    }

    public function show($notification)
    {
        return [
            'title' => $notification['data']['title'],
            'body'  => $notification['data']['body'],
            'data'  => $notification['data']['data'],
            'is_read' => ($notification->read_at == null) ? false : true,
            'date' => $notification->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
