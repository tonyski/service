<?php

namespace Modules\Base\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Auth extends Middleware
{
    /**
     * 可以使用多个guard来认证，必须有一个认证通过，否则抛异常
     */

}
