<?php

namespace App\Models;

use App\User;
use Eloquent;

class ActivityLog extends Eloquent
{
    const UPDATED_AT = null;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id', 'action', 'module', 'route', 'method', 'description', 'ip', 'user_agent', 'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($action, $module = null, $description = null)
    {
        if (!auth()->check()) {
            return null;
        }

        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'route' => request()->route() ? request()->route()->getName() : null,
            'method' => request()->method(),
            'description' => $description,
            'ip' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?: '', 0, 255),
            'created_at' => now(),
        ]);
    }
}