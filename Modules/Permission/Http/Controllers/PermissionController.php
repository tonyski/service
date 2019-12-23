<?php

namespace Modules\Permission\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Base\Contracts\ListServiceInterface;
use Modules\Permission\Http\Requests\PermissionsRequest;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Entities\PermissionGroup;

class PermissionController extends Controller
{
    use PermissionGroup;

    public function fetchPermission(Request $request)
    {
        return $this->successWithData(['permissions' => $request->user()->getAllPermissions()]);
    }

    public function index(PermissionsRequest $request, ListServiceInterface $listService)
    {
        $model = new Permission();
        $paginate = $listService->getList($model, $request);
        $permissionPaginate = $paginate->toArray();

        $data = [];
        $data['permissions'] = $permissionPaginate['data'];
        unset($permissionPaginate['data']);
        $data['paginate'] = $permissionPaginate;

        return $this->successWithData($data);
    }

    public function groups($guard)
    {
        $groups = [];
        $permissions = Permission::where('guard_name', $guard)->get();

        foreach (PermissionGroup::$groups as $g) {
            $groups[$g] = [];
            $groups[$g]['name'] = __('permission::group.' . $g);
            $groups[$g]['permissions'] = [];
        }

        foreach ($permissions as $permission) {
            $groups[$permission->group]['permissions'][] = $permission;
        }

        return $this->successWithData(['groups' => $groups]);
    }

}
