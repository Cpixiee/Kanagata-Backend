<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Log activity to notification system
     */
    public static function logActivity($type, $title, $message, $data = [], $targetUserId = null)
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            // If no user is authenticated (like during testing), create a system notification
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                Notification::createNotification(
                    $admin->id,
                    $type,
                    $title,
                    $message,
                    array_merge($data, [
                        'actor_id' => null,
                        'actor_name' => 'System',
                        'actor_email' => null,
                        'is_system' => true
                    ])
                );
            }
            return;
        }

        // Notify specific user if target is provided
        if ($targetUserId !== null) {
            Notification::createNotification(
                $targetUserId,
                $type,
                $title,
                $message,
                array_merge($data, [
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'actor_email' => $user->email
                ])
            );
            return;
        }

        // If no specific target, notify both admins and the actor (for their own record)
        $usersToNotify = collect();
        
        // Always include admins for oversight
        $adminUsers = User::where('role', 'admin')->get();
        $usersToNotify = $usersToNotify->merge($adminUsers);
        
        // Also notify the current user (for their own activity log) if they're not admin
        if ($user->role !== 'admin') {
            $usersToNotify->push($user);
        }
        
        // Remove duplicates and notify each user
        $usersToNotify->unique('id')->each(function($userToNotify) use ($user, $type, $title, $message, $data) {
            Notification::createNotification(
                $userToNotify->id,
                $type,
                $title,
                $message,
                array_merge($data, [
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'actor_email' => $user->email
                ])
            );
        });
    }

    /**
     * Log activity and notify relevant users based on context
     */
    public static function logActivityWithContext($type, $title, $message, $data = [], $context = [])
    {
        $user = Auth::user();
        if (!$user) return;

        $usersToNotify = collect();
        
        // Always include admins
        $adminUsers = User::where('role', 'admin')->get();
        $usersToNotify = $usersToNotify->merge($adminUsers);
        
        // Add context-based notifications
        if (isset($context['notify_all_users']) && $context['notify_all_users']) {
            // Notify all users
            $allUsers = User::all();
            $usersToNotify = $usersToNotify->merge($allUsers);
        } elseif (isset($context['notify_same_role']) && $context['notify_same_role']) {
            // Notify users with same role
            $sameRoleUsers = User::where('role', $user->role)->get();
            $usersToNotify = $usersToNotify->merge($sameRoleUsers);
        } else {
            // Default: notify the actor user for their own records
            $usersToNotify->push($user);
        }
        
        // Notify each user
        $usersToNotify->unique('id')->each(function($userToNotify) use ($user, $type, $title, $message, $data) {
            Notification::createNotification(
                $userToNotify->id,
                $type,
                $title,
                $message,
                array_merge($data, [
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'actor_email' => $user->email
                ])
            );
        });
    }

    /**
     * Log data creation activity
     */
    public static function logCreate($modelType, $modelName, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Data Baru Ditambahkan";
        $message = "{$user->name} menambahkan {$modelType} baru: {$modelName}";

        // Use context-based notification for create actions
        self::logActivityWithContext(Notification::TYPE_CREATE, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName
        ]), ['notify_same_role' => true]);
    }

    /**
     * Log data update activity
     */
    public static function logUpdate($modelType, $modelName, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Data Diperbarui";
        $message = "{$user->name} memperbarui {$modelType}: {$modelName}";

        // Use context-based notification for update actions
        self::logActivityWithContext(Notification::TYPE_UPDATE, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName
        ]), ['notify_same_role' => true]);
    }

    /**
     * Log data deletion activity
     */
    public static function logDelete($modelType, $modelName, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Data Dihapus";
        $message = "{$user->name} menghapus {$modelType}: {$modelName}";

        // Use context-based notification for delete actions
        self::logActivityWithContext(Notification::TYPE_DELETE, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName
        ]), ['notify_same_role' => true]);
    }

    /**
     * Log approval activity
     */
    public static function logApprove($modelType, $modelName, $requestedBy, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Permintaan Disetujui";
        $message = "{$user->name} menyetujui permintaan {$modelType}: {$modelName} dari {$requestedBy}";

        self::logActivity(Notification::TYPE_APPROVE, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName,
            'requested_by' => $requestedBy
        ]));

        // Also notify the requester
        $requester = User::where('name', $requestedBy)->first();
        if ($requester) {
            $requesterMessage = "Permintaan {$modelType} Anda ({$modelName}) telah disetujui oleh {$user->name}";
            self::logActivity(
                Notification::TYPE_APPROVE, 
                "Permintaan Disetujui", 
                $requesterMessage, 
                $data, 
                $requester->id
            );
        }
    }

    /**
     * Log rejection activity
     */
    public static function logReject($modelType, $modelName, $requestedBy, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Permintaan Ditolak";
        $message = "{$user->name} menolak permintaan {$modelType}: {$modelName} dari {$requestedBy}";

        self::logActivity(Notification::TYPE_REJECT, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName,
            'requested_by' => $requestedBy
        ]));

        // Also notify the requester
        $requester = User::where('name', $requestedBy)->first();
        if ($requester) {
            $requesterMessage = "Permintaan {$modelType} Anda ({$modelName}) telah ditolak oleh {$user->name}";
            self::logActivity(
                Notification::TYPE_REJECT, 
                "Permintaan Ditolak", 
                $requesterMessage, 
                $data, 
                $requester->id
            );
        }
    }

    /**
     * Log mark as paid activity
     */
    public static function logMarkAsPaid($modelType, $modelName, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Status Pembayaran Diubah";
        $message = "{$user->name} mengubah status {$modelType} ({$modelName}) menjadi PAID";

        self::logActivity(Notification::TYPE_MARK_PAID, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'model_name' => $modelName
        ]));
    }

    /**
     * Log schedule activity
     */
    public static function logSchedule($tutorName, $activity, $date, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Jadwal Baru Ditambahkan";
        $message = "{$user->name} menambahkan jadwal untuk tutor {$tutorName}: {$activity} pada {$date}";

        self::logActivity(Notification::TYPE_SCHEDULE, $title, $message, array_merge($data, [
            'tutor_name' => $tutorName,
            'activity' => $activity,
            'schedule_date' => $date
        ]));
    }

    /**
     * Log login activity
     */
    public static function logLogin()
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $title = "Login Berhasil";
        $message = "{$user->name} berhasil masuk ke sistem";

        // Only notify admins for login activities
        self::logActivity(Notification::TYPE_LOGIN, $title, $message, [
            'login_time' => now()->format('Y-m-d H:i:s'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Log review request submission
     */
    public static function logReviewRequest($modelType, $actionType, $modelName, $data = [])
    {
        $user = Auth::user();
        if (!$user) return; // Skip if no authenticated user

        $actionText = [
            'create' => 'penambahan',
            'update' => 'perubahan',
            'delete' => 'penghapusan'
        ][$actionType] ?? $actionType;

        $title = "Permintaan Review Baru";
        $message = "{$user->name} mengirim permintaan {$actionText} {$modelType}: {$modelName}";

        self::logActivity(Notification::TYPE_CREATE, $title, $message, array_merge($data, [
            'model_type' => $modelType,
            'action_type' => $actionType,
            'model_name' => $modelName,
            'is_review_request' => true
        ]));
    }

    /**
     * Notify specific user types
     */
    public static function notifyUserTypes($type, $title, $message, $data = [], $userTypes = ['admin'])
    {
        $user = Auth::user();
        if (!$user) return;

        $usersToNotify = collect();
        
        foreach ($userTypes as $userType) {
            if ($userType === 'all') {
                $allUsers = User::all();
                $usersToNotify = $usersToNotify->merge($allUsers);
            } elseif ($userType === 'admin') {
                $adminUsers = User::where('role', 'admin')->get();
                $usersToNotify = $usersToNotify->merge($adminUsers);
            } elseif ($userType === 'user') {
                $normalUsers = User::where('role', 'user')->get();
                $usersToNotify = $usersToNotify->merge($normalUsers);
            }
        }
        
        // Always include the actor if they're not already in the list
        if (!in_array('all', $userTypes)) {
            $usersToNotify->push($user);
        }
        
        // Notify each user
        $usersToNotify->unique('id')->each(function($userToNotify) use ($user, $type, $title, $message, $data) {
            Notification::createNotification(
                $userToNotify->id,
                $type,
                $title,
                $message,
                array_merge($data, [
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'actor_email' => $user->email
                ])
            );
        });
    }

    /**
     * Create a system-wide announcement
     */
    public static function createAnnouncement($title, $message, $data = [])
    {
        $user = Auth::user();
        if (!$user) return;

        // Notify all users for announcements
        self::notifyUserTypes(
            Notification::TYPE_CREATE,
            $title,
            $message,
            array_merge($data, [
                'is_announcement' => true
            ]),
            ['all']
        );
    }
} 