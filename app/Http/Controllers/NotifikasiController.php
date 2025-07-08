<?php

namespace App\Http\Controllers;

use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function getNotifications()
    {
        $notifications = NotifikasiService::getUserNotifications(Auth::id());
        $unreadCount = NotifikasiService::getUnreadCount(Auth::id());

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function getAdminNotifications()
    {
        $notifications = NotifikasiService::getAdminNotifications();
        $unreadCount = NotifikasiService::getAdminUnreadCount();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request)
    {
        $notifikasi_id = $request->id;
        NotifikasiService::markAsRead($notifikasi_id);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        NotifikasiService::markAllAsRead(Auth::id());

        return response()->json(['success' => true]);
    }
}
