<?php

namespace Modules\Admin\Http\Controllers\Settings;

use Modules\Admin\Http\Controllers\Controller;
use Modules\Admin\Http\Requests\Settings\AccountUpdateRequest;

class AccountController extends Controller
{
    public function update(AccountUpdateRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');

        $admin = $request->user();
        if ($admin->name == $name && $admin->email == $email) {
            return $this->updateSuccess(['admin' => $admin]);
        } elseif ($admin->name != $name && $admin->email != $email) {
            $request->validate(['name' => 'unique:admins', 'email' => 'unique:admins']);
        } else {
            if ($admin->name != $name) {
                $request->validate(['name' => 'unique:admins']);
            }
            if ($admin->email != $email) {
                $request->validate(['email' => 'unique:admins']);
            }
        }

        $flag = $admin->update([
            'name' => $name,
            'email' => $email
        ]);

        return $flag ? $this->updateSuccess(['admin' => $admin]) : $this->failed();
    }
}
