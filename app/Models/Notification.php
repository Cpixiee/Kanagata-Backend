<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    // Constants for notification types
    const TYPE_CREATE = 'create';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
    const TYPE_APPROVE = 'approve';
    const TYPE_REJECT = 'reject';
    const TYPE_MARK_PAID = 'mark_paid';
    const TYPE_SCHEDULE = 'schedule';
    const TYPE_LOGIN = 'login';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk notifikasi yang belum dibaca
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // Scope untuk notifikasi yang sudah dibaca
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    // Method untuk mark as read
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    // Method untuk get icon berdasarkan type
    public function getIconAttribute()
    {
        $icons = [
            self::TYPE_CREATE => 'fas fa-plus-circle text-green-500',
            self::TYPE_UPDATE => 'fas fa-edit text-blue-500',
            self::TYPE_DELETE => 'fas fa-trash text-red-500',
            self::TYPE_APPROVE => 'fas fa-check-circle text-green-500',
            self::TYPE_REJECT => 'fas fa-times-circle text-red-500',
            self::TYPE_MARK_PAID => 'fas fa-money-bill-wave text-yellow-500',
            self::TYPE_SCHEDULE => 'fas fa-calendar-plus text-purple-500',
            self::TYPE_LOGIN => 'fas fa-sign-in-alt text-blue-500',
        ];

        return $icons[$this->type] ?? 'fas fa-bell text-gray-500';
    }

    // Method untuk format time ago
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Method untuk create notification
    public static function createNotification($userId, $type, $title, $message, $data = [])
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }
} 