<?php

namespace Modules\Permission\Http\Controllers;

use Illuminate\Support\Str;
use Modules\Base\Contracts\ListServiceInterface;
use Modules\Permission\Entities\Role;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Http\Requests\RolesRequest;
use Modules\Permission\Http\Requests\CreateRoleRequest;
use Modules\Permission\Http\Requests\EditRoleRequest;
use Modules\Permission\Http\Requests\syncRolePermissionsRequest;

class RoleController extends Controller
{
    public function index(RolesRequest $request, ListServiceInterface $listService)
    {
        $model = new Role();
        $paginate = $listService->getList($model, $request);
        $rolePaginate = $paginate->toArray();

        $data = [];
        $data['roles'] = $rolePaginate['data'];
        unset($rolePaginate['data']);
        $data['paginate'] = $rolePaginate;

        return $this->successWithData($data);
    }

    public function store(CreateRoleRequest $request)
    {
        $role = Role::create([
            'uuid' => Str::uuid()->getHex(),
            'name' => $request->post('name'),
            'guard_name' => $request->post('guard_name'),
            'locale' => $request->post('locale'),
            'comment' => $request->post('comment') ?: '',
        ]);

        return $this->createSuccess(['role' => $role], __('permission::role.addRoleSuccess'));
    }

    public function update(EditRoleRequest $request, $uuid)
    {
        $role = Role::findByUuId($uuid);

        if (!$role->is_system) {
            if ($role->name != $request->input('name')) {
                $request->validate(['name' => 'unique:roles']);
            }

            $flag = $role->update([
                'name' => $request->input('name'),
                'locale' => $request->input('locale'),
                'comment' => $request->input('comment') ?: ''
            ]);
            return $flag ? $this->updateSuccess(['role' => $role]) : $this->failed();
        }

        return $this->failed();
    }

    public function destroy($uuid)
    {
        $role = Role::findByUuId($uuid);
        if (!$role->is_system && $role->delete()) {
            return $this->deleteSuccess();
        }
        return $this->failed();
    }

    public function permissions($uuid)
    {
        $role = Role::findByUuId($uuid);
        return $this->successWithData(['permissions' => $role->permissions]);
    }

    public function syncPermissions(syncRolePermissionsRequest $request, $uuid)
    {
        $role = Role::findByUuId($uuid);

        if (!$role->is_system) {
            $role->permissions()->sync($request->input('permissions'));
            return $this->successWithMessage();
        }

        return $this->failed();
    }

}
