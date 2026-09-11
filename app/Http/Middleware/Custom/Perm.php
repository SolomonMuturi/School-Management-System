<?php

namespace App\Http\Middleware\Custom;

use Closure;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class Perm
{
    /**
     * Handle an incoming request. Gives access to the given module for the
     * current user if their role has been granted it (super admin always passes).
     *
     * Usage: ->middleware('perm:System Settings')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $module
     * @return mixed
     */
    public function handle($request, Closure $next, $module = null)
    {
        if (Auth::check() && Qs::canAccess($module)) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}