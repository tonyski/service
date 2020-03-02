<?php

namespace Modules\Customer\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Modules\Customer\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\Auth\LoginRequest;

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
        return Auth::guard('customer');
    }

    protected function attemptLogin(LoginRequest $request)
    {
        return collect(['name', 'email'])->contains(function ($value) use ($request) {
            $account = $request->get($this->username());
            $password = $request->get('password');
            $remember = $request->get('remember', false);

            $isValidation = false;
            if ($value == 'email' && strpos($account, '@') !== false) {
                $isValidation = true;
            }
            if ($value == 'name' && strpos($account, '@') === false) {
                $isValidation = true;
            }
            if (!$isValidation) {
                return false;
            }

            if ($remember) {
                $this->guard()->setTTL(config('customer.remember_ttl'));
            }

            return $this->guard()->attempt([$value => $account, 'password' => $password]);
        });
    }

    protected function sendLockoutResponse(LoginRequest $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return $this->failedWithMessage(__('customer::auth.throttle', ['seconds' => $seconds]));
    }

    protected function sendFailedLoginResponse()
    {
        return $this->failedWithMessage(__('customer::auth.failed'));
    }

    protected function sendLoginResponse()
    {
        $token = $this->guard()->getToken()->get();
        $expiration = $this->guard()->getPayload()->get('exp');
        $customer = $this->guard()->user();

        $data = [
            'accessToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => $expiration - time(),
            'customer' => $customer,
        ];

        return $this->successWithData($data);
    }

    public function fetchUser()
    {
        $customer = $this->guard()->user();
        $data = [
            'customer' => $customer,
        ];

        return $this->successWithData($data);
    }

    public function logout()
    {
        $this->guard()->logout();
        return $this->success();
    }
}
