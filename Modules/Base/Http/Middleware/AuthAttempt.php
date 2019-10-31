<?php

namespace Modules\Base\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthAttempt extends Middleware
{
    /**
     * 可以使用多个guard来尝试认证，认证通过与否都可以
     */

    public function handle($request, Closure $next, ...$guards)
    {
        $this->attemptAuth($request, $guards);

        return $next($request);
    }

    /**
     * 尝试登录，用于登录和不登录都可以访问的接口。
     * @param $request
     * @param array $guards
     * @return mixed
     */
    protected function attemptAuth($request, array $guards)
    {
        if (empty($guards)) {
            $guards = config('auth.guards');
            $guards = array_keys($guards);
        }
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }
    }
}
