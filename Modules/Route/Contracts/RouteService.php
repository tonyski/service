<?php

namespace Modules\Route\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface RouteService
{
    /**
     * 获取首页入口，根据权限模型对象，返回入口模型对象，或者返回默认的入口
     * @param \Modules\Permission\Contracts\Permission|Null
     * @return \Modules\Route\Entities\Route;
     */
    public function getIndexRoute($permission);

    /**
     * 根据权限集合，返回菜单入口树 数组
     * @param Collection $permissions \Modules\Permission\Contracts\Permission 访问入口权限的集合
     * @return array 菜单入口树的数组
     */
    public function getMenuRouteTree(Collection $permissions);

    /**
     * 根据菜单的ORM集合，返回菜单入口树 数组
     *
     * @param Collection $menus \Modules\Route\Entities\RouteMenu
     * @return array 菜单入口树的数组
     */
    public function menuRouteToTree(Collection $menus);
}