<?php

namespace Modules\Permission\Http\Controllers;

use Modules\Permission\Http\Requests\RolesRequest;
use Modules\Permission\Entities\Role;

class RoleController extends Controller
{
    public function index(RolesRequest $request)
    {
        $limit = $request->input('limit');
        $guardName = $request->input('filter.guard_name');
        $sortName = $request->input('sort.name');

        $roleQuery = Role::query();
        if ($guardName) {
            $roleQuery->where('guard_name', $guardName);
        }
        if ($sortName) {
            $roleQuery->orderBy('name', $sortName);
        }

        $rolePaginate = $roleQuery->paginate($limit)->toArray();

        $data = [];
        $data['roles'] = $rolePaginate['data'];
        unset($rolePaginate['data']);
        $data['paginate'] = $rolePaginate;

        return $this->successWithData($data);
    }
}
