<?php

namespace Modules\Customer\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Modules\Customer\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request)
    {
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->successWithMessage(__($response))
            : $this->failedWithMessage(__($response));
    }

    protected function credentials(ResetPasswordRequest $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    protected function broker()
    {
        return Password::broker('customer');
    }

    protected function guard()
    {
        return Auth::guard('customer');
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);

        $user->save();

//        event(new PasswordReset($user));

//        $this->guard()->login($user);
    }
}
