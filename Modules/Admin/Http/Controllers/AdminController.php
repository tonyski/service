<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Modules\Base\Contracts\ListServiceInterface;
use Modules\Admin\Entities\Admin;
use Modules\Admin\Http\Requests\AdminsRequest;
use Modules\Admin\Http\Requests\CreateAdminRequest;
use Modules\Admin\Http\Requests\EditAdminRequest;
use Modules\Admin\Http\Requests\SyncAdminRolesRequest;
use Modules\Admin\Http\Requests\SyncAdminPermissionsRequest;
use Modules\Admin\Http\Requests\SyncAdminRolesPermissionsRequest;
use Modules\Admin\Notifications\CreateAdmin as CreateAdminNotification;
use Modules\Admin\Notifications\UpdateAdmin as UpdateAdminNotification;

class AdminController extends Controller
{
    public function index(AdminsRequest $request, ListServiceInterface $listService)
    {
        if (!$request->query('page') && !$request->query('limit')) {
            return $this->failed();
        }

        $model = new Admin();
        $admins = $listService->getList($model, $request);
        $data = [];

        $adminPaginate = $admins->toArray();
        $data['admins'] = $adminPaginate['data'];
        unset($adminPaginate['data']);
        $data['paginate'] = $adminPaginate;

        return $this->successWithData($data);
    }

    public function store(CreateAdminRequest $request)
    {
        $password = Str::random(8);

        $admin = Admin::create([
            'uuid' => Str::uuid()->getHex(),
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => Hash::make($password)
        ]);

        $admin->notify(new CreateAdminNotification($password));

        return $this->createSuccess(['admin' => $admin]);
    }

    public function update(EditAdminRequest $request, $uuid)
    {
        $name = $request->input('name');
        $email = $request->input('email');

        $admin = Admin::find($uuid);
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

        $originalName = $admin->name;
        $originalEmail = $admin->email;

        $flag = $admin->update([
            'name' => $name,
            'email' => $email
        ]);

        if ($flag) {
            $admin->notify(new UpdateAdminNotification($originalName, $originalEmail));
        }

        return $flag ? $this->updateSuccess(['admin' => $admin]) : $this->failed();
    }

    public function destroy($uuid)
    {
        $admin = Admin::find($uuid);
//         不需要手动删除角色和权限, Modules\Permission\Entities\Traits\HasRoles 会自动删除
//        $admin->roles()->detach();
//        $admin->permissions()->detach();
        return $admin->delete() ? $this->deleteSuccess() : $this->failed();
    }

    public function roles($uuid)
    {
        $admin = Admin::find($uuid);
        return $this->successWithData(['roles' => $admin->roles]);
    }

    public function syncRoles(SyncAdminRolesRequest $request, $uuid)
    {
        $defaultRole = $request->input('defaultRole');
        $rolesUuid = $request->input('roles');

        $pivot = [];
        foreach ($rolesUuid as $r) {
            $pivot[$r] = ['is_default' => $r == $defaultRole ? 1 : 0];
        }

        $admin = Admin::find($uuid);
        $admin->roles()->sync($pivot);
        return $this->successWithMessage();
    }

    public function permissions($uuid)
    {
        $admin = Admin::find($uuid);
        return $this->successWithData(['permissions' => $admin->permissions]);
    }

    public function syncPermissions(SyncAdminPermissionsRequest $request, $uuid)
    {
        $admin = Admin::find($uuid);
        $admin->permissions()->sync($request->input('permissions'));
        return $this->successWithMessage();
    }

    public function rolesPermissions($uuid)
    {
        $admin = Admin::find($uuid);
        return $this->successWithData(['roles' => $admin->roles, 'permissions' => $admin->permissions]);
    }

    public function syncRolesPermissions(SyncAdminRolesPermissionsRequest $request, $uuid)
    {
        $defaultRole = $request->input('defaultRole');
        $rolesUuid = $request->input('roles');
        $permissions = $request->input('permissions');

        $pivot = [];
        foreach ($rolesUuid as $r) {
            $pivot[$r] = ['is_default' => $r == $defaultRole ? 1 : 0];
        }

        $admin = Admin::find($uuid);
        $admin->roles()->sync($pivot);
        $admin->permissions()->sync($permissions);
        return $this->successWithMessage();
    }
}
