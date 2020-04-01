<?php

namespace Modules\Route\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Route\Contracts\RouteService;
use Modules\Route\Entities\Route;

class MenuRouteTreeService implements RouteService
{
    use MenuRouteTrait;

    public function getIndexRoute($permission)
    {
        if ($permission) {
            return $permission->route;
        }

        $guard = auth()->getDefaultDriver();
        return Route::where(['name' => 'home', 'guard_name' => $guard])->first();
    }

    public function getMenuRouteTree(Collection $permissions)
    {
        $menuRoute = $this->getMenuRouteData($permissions);

        return $this->arrayToTree($menuRoute);
    }

    public function menuRouteToTree(Collection $menus)
    {
        $menuRouteNode = [];
        foreach ($menus as $menu) {
            $menuRouteNode[$menu->uuid] = $menu->attributesToArray();
            $menuRouteNode[$menu->uuid]['node_type'] = 'menu';
            $menuRouteNode[$menu->uuid]['children'] = [];

            if ($menu->routes->count()) {
                foreach ($menu->routes as $route) {
                    $menuRouteNode[$menu->uuid]['children'][] = array_merge(
                        $route->attributesToArray(),
                        [
                            'node_type' => 'route',
                            'parent_uuid' => $route->menu_route->route_menu_uuid,
                            'sort' => $route->menu_route->sort,
                        ]
                    );
                }
            }
        }

        return $this->arrayToTree($menuRouteNode);
    }

    /**
     * 将数组转化成有层级结构的数组，树状结构的数组
     * @param $arr
     * @return array
     */
    private function arrayToTree($arr){
        foreach ($arr as &$node) {
            if ($node['parent_uuid']) {
                $arr[$node['parent_uuid']]['children'][] = &$node;
            }
        }

        $arr = collect($arr)->filter(function ($n) {
            return !$n['parent_uuid'];
        })->values()->toArray();

        $arr = $this->treeChildrenSort($arr);

        return $arr;
    }

    /**
     * 递归排序数组
     *
     * @param  array $treeArr
     * @return array
     */
    private function treeChildrenSort($treeArr)
    {
        $treeArr = collect($treeArr)->sortBy('sort')->all();
        foreach ($treeArr as &$item) {
            if (isset($item['children']) && sizeof($item['children'])) {
                $item['children'] = $this->treeChildrenSort($item['children']);
            }
        }

        return array_values($treeArr);
    }
}
