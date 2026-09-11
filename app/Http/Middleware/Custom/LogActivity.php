<?php

namespace App\Http\Middleware\Custom;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $this->recordIfApplicable($request);

        return $response;
    }

    protected function recordIfApplicable($request)
    {
        try {
            if (!Auth::check()) {
                return;
            }

            $route = $request->route();
            $name = $route ? $route->getName() : $request->path();
            $method = $request->method();

            if (in_array($name, ['users.activity'], true)) {
                return;
            }

            if (in_array($method, ['GET', 'HEAD'], true)) {
                if (!$this->isLoggableGet($name)) {
                    return;
                }
                $action = str_contains($name, 'print') ? 'Printed' : 'Downloaded';
                $module = $this->moduleFromName($name);
                $description = 'Downloaded / printed ' . strtolower($module);
            } elseif ($name === 'login') {
                $action = 'Logged in';
                $module = 'Auth';
                $description = 'User logged into the system';
            } elseif ($name === 'logout') {
                $action = 'Logged out';
                $module = 'Auth';
                $description = 'User logged out of the system';
            } else {
                $action = $this->actionFromRoute($name, $method);
                $module = $this->moduleFromName($name);
                $description = ucwords(str_replace(['-', '_', '.'], ' / ', $name));
                $id = $this->entityId($request);
                $description = $id ? $description . ' (ID: ' . $id . ')' : $description;
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'route' => $name,
                'method' => $method,
                'description' => $description,
                'ip' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?: '', 0, 255),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Logging must never break the request.
        }
    }

    protected function isLoggableGet($name)
    {
        return str_contains($name, 'print')
            || str_contains($name, 'download')
            || str_contains($name, 'receipts.pdf');
    }

    protected function actionFromRoute($name, $method)
    {
        if ($method === 'DELETE' || str_contains($name, '.destroy')) {
            return 'Deleted';
        }
        if (str_contains($name, 'reset_pass')) {
            return 'Password reset';
        }
        if (str_contains($name, 'toggle')) {
            return 'Status toggled';
        }
        if (str_contains($name, 'void')) {
            return 'Voided';
        }
        if (str_contains($name, 'revoke')) {
            return 'Revoked';
        }
        if (str_contains($name, 'verify')) {
            return 'Verified';
        }
        if (str_contains($name, 'promote')) {
            return 'Promoted';
        }
        if (str_contains($name, 'store')) {
            return 'Created / Saved';
        }
        if ($method === 'PUT' || $method === 'PATCH' || str_contains($name, 'update')) {
            return 'Updated';
        }

        return 'Saved';
    }

    protected function moduleFromName($name)
    {
        $first = strtok($name, '.');
        return $first ? ucwords(str_replace('_', ' ', $first)) : 'System';
    }

    protected function entityId($request)
    {
        $params = $request->route() ? $request->route()->parameters() : [];
        foreach ($params as $value) {
            if (is_numeric($value) || (is_string($value) && ctype_digit(substr($value, 0, 10)))) {
                return $value;
            }
        }
        return null;
    }
}