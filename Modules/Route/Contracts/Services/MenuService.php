<?php

namespace Modules\Route\Contracts\Services;

use Modules\Route\Entities\RouteMenu as Menu;

interface MenuService
{
    public function getMenuByUuid($uuid): Menu;

    public function addMenu(Menu $menu): Menu;

    public function updateMenuByUuid(Menu $menu): int;
}
