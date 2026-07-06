<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Settings::where('id', 1)->first();
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);
        
        return view('user.notifications.index', [
            'notifications' => $notifications,
            'settings' => $settings,
            'title' => 'Notifications',
        ]);
    }

    /**
     * Get unread notifications count for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications for dropdown.
     *
     * @param  int  $limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatest(Request $request)
    {
        $limit = $request->input('limit', 5);
        
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->take($limit)
            ->get();
        
        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        // Transform the notifications to ensure consistent date formatting
        $transformedNotifications = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'user_id' => $notification->user_id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => (bool) $notification->is_read,
                'link' => $notification->link,
                'icon' => $notification->icon,
                'icon_bg_color' => $notification->icon_bg_color,
                'created_at' => $notification->created_at->toISOString(),
                'updated_at' => $notification->updated_at->toISOString(),
            ];
        });
        
        return response()->json([
            'notifications' => $transformedNotifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->is_read = true;
        $notification->save();
        
        // If the notification has a link, redirect to it
        if ($notification->link) {
            return redirect($notification->link);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read for the current user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a specific notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->delete();
        
        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }

    /**
     * Delete all notifications for the current user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        Notification::where('user_id', Auth::id())->delete();
        
        return redirect()->back()->with('success', 'All notifications deleted successfully.');
    }
} 