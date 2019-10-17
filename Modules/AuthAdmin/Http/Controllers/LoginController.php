<?php

namespace Modules\AuthAdmin\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Modules\AuthAdmin\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    use ThrottlesLogins;

    public $maxAttempts = 3;
    public $decayMinutes = 1440;

    public function login(LoginRequest $request)
    {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse();
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse();
    }

    public function username()
    {
        return 'username';
    }

    public function guard()
    {
        return Auth::guard('admin');
    }

    protected function attemptLogin(LoginRequest $request)
    {
        return collect(['name', 'email'])->contains(function ($value) use ($request) {
            $account = $request->get($this->username());
            $password = $request->get('password');
            $remember = $request->get('remember', false);

            //当请求登录的值是邮箱格式，且 不是使用邮箱字段验证，则不用验证
            if (strpos($account, '@') !== false && $value != 'email') {
                return false;
            }

            if ($remember) {
                $this->guard()->setTTL(config('authadmin.remember_ttl'));
            }

            return $this->guard()->attempt([$value => $account, 'password' => $password]);
        });
    }

    protected function sendLockoutResponse(LoginRequest $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return $this->failedWithMessage(__('authadmin::auth.throttle', ['seconds' => $seconds]));
    }

    protected function sendFailedLoginResponse()
    {
        return $this->failedWithMessage(__('authadmin::auth.failed'));
    }

    protected function sendLoginResponse()
    {
        $token = $this->guard()->getToken()->get();
        $expiration = $this->guard()->getPayload()->get('exp');
        $admin = $this->guard()->user();

        $data = [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $expiration - time(),
            'admin' => $admin,
        ];

        return $this->successWithData($data);
    }

    public function fetchUser()
    {
        $admin = $this->guard()->user();
        $data = [
            'admin' => $admin,
        ];

        return $this->successWithData($data);
    }

    public function logout()
    {
        $this->guard()->logout();
        return $this->success();
    }
}