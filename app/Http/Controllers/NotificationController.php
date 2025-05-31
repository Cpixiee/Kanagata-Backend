<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Notification::where('user_id', $user->id)
                    ->orderBy('created_at', 'desc');

        // Filter by read status if specified
        if ($request->has('unread_only') && $request->unread_only) {
            $query->unread();
        }

        $notifications = $query->limit(20)->get();
        
        // Get unread count
        $unreadCount = Notification::where('user_id', $user->id)
                        ->unread()
                        ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'time_ago' => $notification->time_ago,
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    'data' => $notification->data
                ];
            }),
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Verify the notification belongs to the current user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Notification::where('user_id', $user->id)
                   ->whereNull('read_at')
                   ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        // Verify the notification belongs to the current user
        if ($notification->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Get unread count only
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        
        $unreadCount = Notification::where('user_id', $user->id)
                        ->unread()
                        ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Test notification page
     */
    public function testNotification()
    {
        return response()->json([
            'message' => 'Notification test endpoint is available',
            'instructions' => 'Use POST /test-notification/create with type parameter'
        ]);
    }

    /**
     * Create test notifications
     */
    public function createTestNotification(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'admin':
                NotificationService::notifyUserTypes(
                    Notification::TYPE_CREATE,
                    'Test Admin Notification',
                    "Test notification sent by {$user->name} for admin users only",
                    ['test' => true, 'timestamp' => now()],
                    ['admin']
                );
                break;
                
            case 'user':
                NotificationService::notifyUserTypes(
                    Notification::TYPE_UPDATE,
                    'Test User Notification',
                    "Test notification sent by {$user->name} for regular users only",
                    ['test' => true, 'timestamp' => now()],
                    ['user']
                );
                break;
                
            case 'all':
                NotificationService::createAnnouncement(
                    'Test System Announcement',
                    "System-wide test announcement created by {$user->name}",
                    ['test' => true, 'timestamp' => now()]
                );
                break;
                
            case 'self':
                Notification::createNotification(
                    $user->id,
                    Notification::TYPE_LOGIN,
                    'Personal Test Notification',
                    "Personal test notification for {$user->name}",
                    ['test' => true, 'timestamp' => now()]
                );
                break;
                
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid type. Use: admin, user, all, or self'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Test notification of type '{$type}' created successfully",
            'created_by' => $user->name,
            'type' => $type
        ]);
    }
} 