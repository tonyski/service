<?php

namespace Modules\Route\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Permission\Contracts\PermissionService;
use Modules\Route\Contracts\RouteService;
use Modules\Route\Entities\RouteMenu as Menu;
use Modules\Route\Http\Requests\CreateMenuRequest;
use Modules\Route\Http\Requests\EditMenuRequest;
use Modules\Route\Http\Requests\AddMenuRoutesRequest;

use Modules\Route\Contracts\Services\MenuService;

class MenuController extends Controller
{
    private $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * 获取当前登录用户的，首页和侧边栏
     */
    public function fetchMenu(Request $request, PermissionService $permissionService, RouteService $routeService)
    {
        $data = [];

        $admin = $request->user();

        //获取当前登录用户的首页
        $data['index'] = $routeService->getIndexRoute($permissionService->getUserIndexPermission($admin));
        //获取当前登录用户的所有访问入口和侧边栏分类
        $data['menu'] = $routeService->getMenuRouteTree($permissionService->getUserRoutePermission($admin));

        return $this->successWithData($data);
    }

    public function store(CreateMenuRequest $request)
    {
        $menu = new Menu([
            'parent_uuid' => $request->post('parent_uuid') ?: '',
            'guard_name' => $request->post('guard_name'),
            'name' => $request->post('name'),
            'icon' => $request->post('icon') ?: '',
            'locale' => $request->post('locale'),
            'comment' => $request->post('comment') ?: '',
        ]);

        $menu = $this->menuService->addMenu($menu);

        return $this->createSuccess(['menu' => $menu]);
    }

    public function update(EditMenuRequest $request, $uuid)
    {
        $menu = new Menu([
            'uuid' => $uuid,
            'name' => $request->input('name'),
            'icon' => $request->input('icon') ?: '',
            'locale' => $request->input('locale'),
            'comment' => $request->input('comment') ?: '',
        ]);

        $flag = $this->menuService->updateMenuByUuid($menu);

        $flag ? $menu = $this->menuService->getMenuByUuid($uuid) : '';

        return $flag ? $this->updateSuccess(['menu' => $menu]) : $this->failed();
    }

    public function routes($uuid)
    {
        $menu = Menu::find($uuid);
        return $this->successWithData(['routes' => $menu->routes]);
    }

    public function addRoutes(AddMenuRoutesRequest $request, $uuid)
    {
        $routes = $request->input('routes');

        $menu = Menu::find($uuid);
        $sort = $menu->children()->count() + $menu->routes()->count();

        $addRoutes = [];
        foreach ($routes as $k => $routeUuid) {
            $addRoutes[$routeUuid] = ['sort' => $sort + $k];
        }

        $menu->routes()->syncWithoutDetaching($addRoutes);
        return $this->successWithMessage();
    }
}
