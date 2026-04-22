<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
                            ->latest()
                            ->paginate(20);

        // Mark all as read
        Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

        return view('tenant.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
                    ->where('is_read', false)
                    ->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }

    public function destroyAll()
    {
        Notification::where('user_id', auth()->id())->delete();
        return back()->with('success', 'All notifications cleared.');
    }

    // Get unread count for navbar
    public static function unreadCount(): int
    {
        if (!auth()->check()) return 0;
        return Notification::where('user_id', auth()->id())
                           ->where('is_read', false)
                           ->count();
    }
}