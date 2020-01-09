<?php

namespace Modules\Route\Services;

class MenuRouteTreeService
{
    /**
     * 将 菜单和入口的 ORM模型集合转换成树状数组
     *
     * @param  $menus  菜单和入口的模型的集合
     * @return array
     */
    public static function menuRouteToTree($menus)
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

        foreach ($menuRouteNode as &$node) {
            if ($node['parent_uuid']) {
                $menuRouteNode[$node['parent_uuid']]['children'][] = &$node;
            }
        }

        $menuRouteTree = collect($menuRouteNode)->filter(function ($menu) {
            return !$menu['parent_uuid'];
        })->values()->toArray();

        $menuRouteTree = MenuRouteTreeService::treeChildrenSort($menuRouteTree);

        return $menuRouteTree;
    }

    /**
     * 递归排序数组
     *
     * @param  array $treeArr
     * @return array
     */
    public static function treeChildrenSort($treeArr)
    {
        $treeArr = collect($treeArr)->sortBy('sort')->all();
        foreach ($treeArr as &$item) {
            if (isset($item['children']) && sizeof($item['children'])) {
                $item['children'] = MenuRouteTreeService::treeChildrenSort($item['children']);
            }
        }

        return array_values($treeArr);
    }
}
