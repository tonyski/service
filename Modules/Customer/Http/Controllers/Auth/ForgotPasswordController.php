<?php

namespace Modules\Customer\Http\Controllers\Auth;

use Illuminate\Support\Facades\Password;
use Modules\Customer\Http\Controllers\Controller;
use Modules\Customer\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->successWithMessage(__($response))
            : $this->failedWithMessage(__($response));
    }

    protected function broker()
    {
        return Password::broker('customer');
    }

    protected function credentials(ForgotPasswordRequest $request)
    {
        return $request->only('email');
    }
}
