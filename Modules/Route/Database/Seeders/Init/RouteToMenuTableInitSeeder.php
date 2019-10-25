<?php

namespace Modules\Route\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Modules\Route\Entities\RouteMenu as Menu;
use Modules\Route\Entities\Route;

class RouteToMenuTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            if (is_array($item['route'])) {
                $menu = Menu::where(['name' => $item['menu'], 'guard_name' => Menu::GUARD_ADMIN])->first();
                $routes = Route::where('guard_name', Menu::GUARD_ADMIN)
                    ->whereIn('name', $item['route'])
                    ->get()
                    ->map->uuid
                    ->all();

                $routes = array_flip($routes);
                array_walk($routes, function (&$route) {
                    $route = ['sort' => ++$route];
                });

                $menu->routes()->sync($routes);
            }
        });
    }

    private function getData()
    {
        return $this->getDataFromFile(Menu::GUARD_ADMIN);
    }

    private function getDataFromFile($guardName)
    {
        return json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/route_to_menu.json'), true);
    }
}
