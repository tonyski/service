<?php

namespace Modules\Route\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Modules\Route\Contracts\Services\MenuService as MenuServiceContract;
use Modules\Route\Contracts\Repositories\MenuRepository;
use Modules\Route\Entities\RouteMenu as Menu;

class MenuService implements MenuServiceContract
{
    private $menuRepository;

    public function __construct(MenuRepository $repository)
    {
        $this->menuRepository = $repository;
    }

    public function getMenuByUuid($uuid): Menu
    {
        return $this->menuRepository->findMenuByUuid($uuid);
    }

    public function addMenu(Menu $menu): Menu
    {
        $menu->uuid = Str::uuid()->getHex();

        $sort = $this->menuRepository->getChildrenCountByUuid($menu->parent_uuid);
        if ($menu->parent_uuid) {
            $sort += $this->menuRepository->getRoutesCountByUuid($menu->parent_uuid);
        }
        $menu->sort = $sort;

        return $this->menuRepository->createMenu($menu);
    }

    public function updateMenuByUuid(Menu $menu): int
    {
        $m = $this->menuRepository->findMenuByUuid($menu->uuid);

        if ($m->name != $menu->name) {
            Validator::make(['name'=>$menu->name], [
                'name' => 'unique:route_menus'
            ])->validate();
        }

        return $this->menuRepository->updateMenuByUuid($menu);
    }

}