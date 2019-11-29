<?php

namespace Modules\Permission\Http\Controllers;

use Modules\Permission\Http\Requests\RolesRequest;
use Modules\Permission\Entities\Role;
use Modules\Base\Contracts\ListServiceInterface;

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
}
