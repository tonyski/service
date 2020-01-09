<?php

namespace Modules\Route\Http\Controllers;

use Modules\Route\Entities\RouteMenu as Menu;

class MenuTreeController extends Controller
{
    public function menuTreeNodeTop($guard)
    {
        $menuTree = Menu::where(['parent_uuid' => '', 'guard_name' => $guard])->get();
        return $this->successWithData(['node' => ['children' => $menuTree, 'routes' => []]]);
    }

    public function menuTreeNode($guard, $uuid)
    {
        $menu = Menu::where('uuid', $uuid)->first();
        $menu->children;
        $menu->routes;
        return $this->successWithData(['node' => $menu]);
    }
}
