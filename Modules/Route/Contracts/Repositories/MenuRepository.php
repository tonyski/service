<?php

namespace Modules\Route\Contracts\Repositories;

use Modules\Route\Entities\RouteMenu as Menu;

interface MenuRepository
{
    public function createMenu(Menu $menu): Menu;

    public function getChildrenCountByUuid($uuid): int;

    public function getRoutesCountByUuid($uuid): int;

    public function findMenuByUuid($uuid): Menu;

    public function updateMenuByUuid(Menu $menu): int;
}