<?php

namespace Modules\Permission\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Base\Contracts\ListServiceInterface;
use Modules\Permission\Http\Requests\PermissionsRequest;
use Modules\Permission\Http\Requests\GroupsRequest;
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
        $permissions = $listService->getList($model, $request);
        $data = [];

        if ($permissions instanceof LengthAwarePaginator) {
            $permissionPaginate = $permissions->toArray();
            $data['permissions'] = $permissionPaginate['data'];
            unset($permissionPaginate['data']);
            $data['paginate'] = $permissionPaginate;
        } else {
            $data['permissions'] = $permissions->toArray();
        }

        return $this->successWithData($data);
    }

    public function groups(GroupsRequest $request)
    {
        $guard = $request->query('filter')['guard_name'];

        $groups = [];
        $permissions = Permission::where('guard_name', $guard)->get();

        foreach (PermissionGroup::$groups[$guard] as $g) {
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
