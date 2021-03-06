<?php

namespace Modules\Admin\Http\Controllers\Settings;

use Illuminate\Support\Facades\Hash;
use Modules\Admin\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\Settings\PasswordUpdateRequest;
use Modules\Admin\Events\PasswordReset;

class PasswordController extends Controller
{
    public function update(PasswordUpdateRequest $request)
    {
        $passwordOld = $request->input('password_old');
        $passwordNew = $request->input('password');

        $admin = $request->user();
        if (Hash::check($passwordOld, $admin->password)) {
            $admin->password = Hash::make($passwordNew);
            $admin->save();
            $admin->incrementJWTVersion();

            broadcast(new PasswordReset($admin))->toOthers();
            return $this->successWithMessage();
        }

        return $this->failedWithMessage(__('admin::settings.passwordError'));
    }
}
