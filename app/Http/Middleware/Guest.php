<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\Auth\UnGuestException;

class Guest
{
    public function handle($request, Closure $next, $guard = null)
    {
        if ($guard) {
            Auth::shouldUse($guard);
        }

        if (Auth::guard($guard)->check()) {
            throw new UnGuestException(__('auth.unGuest'));
        }

        return $next($request);
    }
}
