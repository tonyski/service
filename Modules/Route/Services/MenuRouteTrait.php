<?php

namespace Modules\Route\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Route\Entities\RouteMenu;

/**
 * 根据权限集合 获取所有入口->对应的菜单->对应的父级菜单
 */

trait MenuRouteTrait
{
    /**
     * @param Collection $permissions
     * @return array
     */
    public function getMenuRouteData(Collection $permissions)
    {
        //加载入口权限对应的入口,对应的侧边栏
        $permissions->loadMissing(['route', 'route.menus']);

        //入口映射到侧边栏的数组
        $routeToMenu = [];
        //获取用户的所有访问入口
        $routes = $permissions->map(function ($permission) {
            return $permission->route;
        })->keyBy(function ($route) {
            return $route->getKey();
        });
        //获取访问入口对应的侧边栏 , 保留入口和侧边栏的对应关系到数组
        $menus = $routes->flatMap(function ($route) use (&$routeToMenu) {
            $route->menus->each(function ($menu) use (&$routeToMenu) {
                $routeToMenu[] = $menu->route_menu->toArray();
            });
            return $route->menus;
        })->keyBy(function ($menu) {
            return $menu->getKey();
        });
        //将入口按照侧边栏进行分组，并排序
        $menuToRoute = collect($routeToMenu)->sortBy('sort')->groupBy('route_menu_uuid');

        //获取所有侧边栏的所有父级侧边栏
        $this->collectParentMenus($menus);

        // 返回数据
        $data = [];
        foreach ($menus as $menu) {
            $data[$menu->uuid] = array_merge($menu->attributesToArray(), [
                'node_type' => 'menu',
                'children' => $this->getMenuRoutes($menu->uuid, $menuToRoute, $routes),
            ]);
        }

        return $data;
    }

    /**
     * 收集菜单集合的所有父级菜单
     * @param Collection $menus
     */
    private function collectParentMenus($menus)
    {
        // foreach 开始时 已经将集合的元素创建了迭代器，循环运行中添加到集合的元素，不会循环。
        foreach ($menus as $menu) {
            $this->collectParentMenu($menus, $menu);
        }
    }

    /**
     * 递归获取父级菜单
     * @param Collection $menus  容器
     * @param $menu              菜单对象
     */
    private function collectParentMenu($menus, $menu)
    {
        $parentUuid = $menu->parent_uuid;

        if (!$parentUuid || $menus->has($parentUuid)) {
            return;
        }

        $parentMenu = RouteMenu::find($parentUuid);
        $menus->put($parentUuid, $parentMenu);
        $this->collectParentMenu($menus, $parentMenu);
    }

    private function getMenuRoutes($menuUuid, $menuToRoute, $routes)
    {
        $routeList = [];
        if ($menuToRoute->has($menuUuid)) {
            $menuToRoute->get($menuUuid)->each(function ($menuRoute) use (&$routeList, $routes) {
                $route = $routes->get($menuRoute['route_uuid']);
                $routeList[] = array_merge($route->attributesToArray(), [
                    'node_type' => 'route',
                    'parent_uuid' => $menuRoute['route_menu_uuid'],
                    'sort' => $menuRoute['sort']
                ]);
            });
        }

        return $routeList;
    }
}
