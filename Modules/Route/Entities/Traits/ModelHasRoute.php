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
        $this->getMenuList($menus);
        //将数据组成树状结构
        return $this->getMenuTree($menus, $menuToRoute, $routes);
    }

    /**
     * @param Collection $menus 需要查找其父级分类的集合
     */
    public function getMenuList(Collection $menus)
    {
        // foreach 开始时 已经将集合的元素创建了迭代器，循环运行中添加到集合的元素，不会循环。
        foreach ($menus as $menu) {
            $this->getParentMenuTree($menus, $menu);
        }
    }

    /**
     * @param Collection $menus 已经查出来了的分类的集合
     * @param RouteMenu $menu   查询此分类的所有父级分类
     */
    public function getParentMenuTree(Collection $menus, RouteMenu $menu)
    {
        $parentUuid = $menu->parent_uuid;

        if (!$parentUuid || $menus->has($parentUuid)) {
            return;
        }

        $parentMenu = RouteMenu::find($parentUuid);
        $menus->put($parentUuid, $parentMenu);
        $this->getParentMenuTree($menus, $parentMenu);
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

        foreach ($menuTree as &$menuNode) {
            if ($menuNode['parent_uuid']) {
                $menuTree[$menuNode['parent_uuid']]['menu'][] = &$menuNode;
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
