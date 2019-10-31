<?php

namespace Modules\Route\Http\Controllers;

use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * 获取当前登录用户的，首页和侧边栏
     */
    public function fetchMenu(Request $request)
    {
        $data = [];
        //获取当前登录用户的首页
        $data['index'] = $request->user()->getRouteIndex();
        //获取当前登录用户的所有访问入口和侧边栏分类
        $data['menu'] = $request->user()->getRouteMenu();

        return $this->successWithData($data);
    }
}
