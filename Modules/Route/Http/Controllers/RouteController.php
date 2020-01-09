<?php

namespace Modules\Route\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Base\Contracts\ListServiceInterface;
use Modules\Route\Entities\Route;
use Modules\Route\Http\Requests\RoutesRequest;

class RouteController extends Controller
{
    public function index(RoutesRequest $request, ListServiceInterface $listService)
    {
        $model = new Route();
        $routes = $listService->getList($model, $request);
        $data = [];

        if ($routes instanceof LengthAwarePaginator) {
            $routePaginate = $routes->toArray();
            $data['routes'] = $routePaginate['data'];
            unset($routePaginate['data']);
            $data['paginate'] = $routePaginate;
        } else {
            $data['routes'] = $routes->toArray();
        }

        return $this->successWithData($data);
    }
}
