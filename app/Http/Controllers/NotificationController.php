<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return $this->successfulResponse(200, $notifications);
    }

    public function unread(Request $request)
    {
        $unread = auth()->user()->unreadNotifications->count();

        return $this->successfulResponse(200, $unread);
    }

    public function markAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function recent()
    {
        $notifications = auth()->user()->notifications()->take(5)->get()->pluck('data');

        return $this->successfulResponse(200, $notifications);
    }
}
