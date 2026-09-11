<?php

namespace App\Models;

use App\User;
use Eloquent;
use Illuminate\Support\Facades\DB;

class AppNotification extends Eloquent
{
    const UPDATED_AT = null;

    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'link', 'is_read', 'created_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public static function createFor($userIds, $title, $message, $link = null, $type = 'info', $createdAt = null)
    {
        $recipients = is_array($userIds) ? $userIds : [$userIds];
        $recipients = array_values(array_unique(array_map('intval', array_filter($recipients))));
        if (empty($recipients)) {
            return 0;
        }

        $createdAt = $createdAt ?: now()->toDateTimeString();
        $links = is_array($link) ? $link : [];

        $rows = array_map(function ($uid) use ($title, $message, $type, $createdAt, $link, $links) {
            $notifLink = isset($links[$uid]) ? $links[$uid] : $link;
            return [
                'user_id' => $uid,
                'title' => $title,
                'message' => $message,
                'link' => $notifLink,
                'type' => $type,
                'is_read' => 0,
                'created_at' => $createdAt,
            ];
        }, $recipients);

        return DB::table('app_notifications')->insert($rows);
    }

    public static function unreadCount($userId)
    {
        return static::where('user_id', $userId)->where('is_read', false)->count();
    }
}