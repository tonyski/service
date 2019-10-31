<?php

namespace Modules\Route\Entities\Traits;

use Illuminate\Support\Collection;
use Modules\Route\Entities\RouteMenu;
use Modules\Route\Entities\Route;

trait ModelHasRoute
{
    private $indexRoute = null; // 用户的首页入口

    /**
     * 返回用户的首页入口信息
     */
    public function getRouteIndex()
    {
        if (is_null($this->indexRoute)) {
            $permission = $this->getIndexPermissions();

            if (is_null($permission)) {
                $this->indexRoute = Route::where('name', 'home')->first();
            } else {
                $this->indexRoute = $permission->route;
            }
        }

        return [
            'uuid' => $this->indexRoute->uuid,
            'name' => $this->indexRoute->name,
            'route' => $this->indexRoute->route,
            'locale' => $this->indexRoute->getLocale(),
            'comment' => $this->indexRoute->comment
        ];
    }

    /**
     * 返回入口权限 和 侧边栏菜单
     */
    public function getRouteMenu()
    {
        //获取用户的所有访问入口权限
        $permissions = $this->getRoutePermissions();
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
        $this->getMenuList($menus, $menus);
        //将数据组成树状结构
        return $this->getMenuTree($menus, $menuToRoute, $routes);
    }

    /**
     * @param Collection $list 存放已经查出来的模型集合
     * @param Collection $menus 需要查询父级模型的模型集合
     */
    public function getMenuList(Collection &$list, Collection $menus)
    {
        foreach ($menus as $menu) {
            $this->getParentMenuTree($list, $menu);
        }
    }

    /**
     * @param Collection $list 存放已经查出来的模型集合
     * @param RouteMenu $menu 模型对象
     */
    public function getParentMenuTree(Collection &$list, RouteMenu $menu)
    {
        $parentUuid = $menu->parent_uuid;

        if (!$parentUuid || $list->has($parentUuid)) {
            return;
        }

        $parentMenu = RouteMenu::find($parentUuid);
        $list->put($parentUuid, $parentMenu);
        $this->getParentMenuTree($list, $parentMenu);
    }

    public function getMenuTree(Collection $menus, Collection $menuToRoute, Collection $routes)
    {
        $menuTree = [];
        foreach ($menus as $menu) {
            $menuTree[$menu->uuid] = [
                'uuid' => $menu->uuid,
                'parent_uuid' => $menu->parent_uuid,
                'name' => $menu->name,
                'icon' => $menu->icon,
                'comment' => $menu->comment,
                'locale' => $menu->getLocale(),
                'sort' => $menu->sort,
                'route' => $this->getRoutes($menu->uuid, $menuToRoute, $routes),
                'menu' => [],
            ];
        }

        foreach ($menuTree as &$menu) {
            if ($menu['parent_uuid']) {
                $menuTree[$menu['parent_uuid']]['menu'][] = $menu;
            }
        }

        return collect($menuTree)->filter(function ($menu) {
            return !$menu['parent_uuid'];
        })->sortBy('sort')->values();
    }

    public function getRoutes($menuUuid, Collection $menuToRoute, Collection $routes)
    {
        $routeList = [];
        if ($menuToRoute->has($menuUuid)) {
            $menuToRoute->get($menuUuid)->each(function ($menuRoute) use (&$routeList, $routes) {
                $route = $routes->get($menuRoute['route_uuid']);
                $routeList[] = [
                    'uuid' => $route->uuid,
                    'name' => $route->name,
                    'route' => $route->route,
                    'locale' => $route->getLocale(),
                    'comment' => $route->comment,
                    'sort' => $menuRoute['sort']
                ];
            });
        }

        return $routeList;
    }
}
