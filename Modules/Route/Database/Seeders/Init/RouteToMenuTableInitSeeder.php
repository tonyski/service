<?php

namespace Modules\Route\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Modules\Permission\Entities\PermissionType;
use Modules\Route\Entities\RouteMenu as Menu;
use Modules\Route\Entities\Route;

class RouteToMenuTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            if (is_array($item['route'])) {
                $menu = Menu::where(['name' => $item['menu'], 'guard_name' => PermissionType::$GUARD_ADMIN])->first();

                $routes = Route::where('guard_name', PermissionType::$GUARD_ADMIN)
                    ->whereIn('name', $item['route'])
                    ->get();

                $sort = array_flip($item['route']);

                $syncRoutes = [];
                foreach ($routes as $route) {
                    $syncRoutes[$route->uuid] = ['sort' => $sort[$route->name]];
                }

                $menu->routes()->sync($syncRoutes);
            }
        });
    }

    private function getData()
    {
        return $this->getDataFromFile(PermissionType::$GUARD_ADMIN);
    }

    private function getDataFromFile($guardName)
    {
        return json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/route_to_menu.json'), true);
    }
}
