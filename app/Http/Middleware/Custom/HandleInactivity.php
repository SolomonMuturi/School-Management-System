<?php

namespace App\Http\Middleware\Custom;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class HandleInactivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $timeout = (int) config('auth.inactivity_timeout', 7) * 60;
            $last = Session::get('last_activity');

            if ($last && (time() - (int) $last) > $timeout) {
                Auth::logout();
                Session::flush();

                return redirect()->route('login')->with('flash_info', 'You have been logged out due to inactivity.');
            }

            Session::put('last_activity', time());
        } else {
            Session::forget('last_activity');
        }

        return $next($request);
    }
}