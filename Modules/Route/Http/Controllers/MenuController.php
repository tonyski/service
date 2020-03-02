<?php

namespace Modules\Route\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Route\Entities\RouteMenu as Menu;
use Modules\Route\Http\Requests\CreateMenuRequest;
use Modules\Route\Http\Requests\EditMenuRequest;
use Modules\Route\Http\Requests\AddMenuRoutesRequest;

class MenuController extends Controller
{
    /**
     * 获取当前登录用户的，首页和侧边栏
     */
    public function fetchMenu(Request $request)
    {
        $data = [];
        //获取当前登录用户的首页
        $data['index'] = $request->user()->getIndexRoute();
        //获取当前登录用户的所有访问入口和侧边栏分类
        $data['menu'] = $request->user()->getRouteMenuTree();

        return $this->successWithData($data);
    }

    public function store(CreateMenuRequest $request)
    {
        $guardName = $request->post('guard_name');
        $parentUuid = $request->post('parent_uuid') ?: '';
        $sort = Menu::where(['guard_name' => $guardName, 'parent_uuid' => $parentUuid])->count();

        if ($parentUuid) {
            $sort += Menu::find($parentUuid)->routes()->count();
        }

        $menu = Menu::create([
            'uuid' => Str::uuid()->getHex(),
            'parent_uuid' => $parentUuid,
            'guard_name' => $guardName,
            'name' => $request->post('name'),
            'icon' => $request->post('icon') ?: '',
            'locale' => $request->post('locale'),
            'comment' => $request->post('comment') ?: '',
            'sort' => $sort,
        ]);

        return $this->createSuccess(['menu' => $menu]);
    }

    public function update(EditMenuRequest $request, $uuid)
    {
        $menu = Menu::where('uuid', $uuid)->first();

        if ($menu->name != $request->input('name')) {
            $request->validate(['name' => 'unique:route_menus']);
        }

        $flag = $menu->update([
            'name' => $request->input('name'),
            'icon' => $request->input('icon') ?: '',
            'locale' => $request->input('locale'),
            'comment' => $request->input('comment') ?: '',
        ]);
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
