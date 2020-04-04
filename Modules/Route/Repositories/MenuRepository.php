<?php

namespace Modules\Route\Repositories;

use Modules\Route\Contracts\Repositories\MenuRepository as MenuRepositoryContract;
use Modules\Route\Entities\RouteMenu as Menu;

class MenuRepository implements MenuRepositoryContract
{
    public function createMenu(Menu $menu): Menu
    {
        return tap($menu, function ($instance) {
            $instance->save();
        });
    }

    public function getChildrenCountByUuid($uuid): int
    {
        return Menu::where(['parent_uuid' => $uuid])->count();
    }

    public function getRoutesCountByUuid($uuid): int
    {
        return Menu::find($uuid)->routes()->count();
    }

    public function findMenuByUuid($uuid): Menu
    {
        return Menu::find($uuid);
    }

    public function updateMenuByUuid(Menu $menu): int
    {
        return Menu::where('uuid', $menu->uuid)->update([
            'name' => $menu->name,
            'icon' => $menu->icon,
            'locale' => $menu->locale,
            'comment' => $menu->comment,
        ]);
    }


}