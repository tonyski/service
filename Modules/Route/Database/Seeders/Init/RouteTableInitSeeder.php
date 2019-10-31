<?php

namespace Modules\Route\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Entities\PermissionType;
use Modules\Route\Entities\Route;

class RouteTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            Route::firstOrCreate(
                ['name' => $item['name'], 'guard_name' => PermissionType::$GUARD_ADMIN],
                $item
            );
        });
    }

    private function getData()
    {
        $routes = [];
        $indexRoute = $this->getDataFromFile(PermissionType::$GUARD_ADMIN, 'index');
        $routeRoute = $this->getDataFromFile(PermissionType::$GUARD_ADMIN, 'route');

        $lists = array_merge($indexRoute, $routeRoute);

        array_walk($lists, function ($list) use (&$routes) {
            $list['uuid'] = Permission::where(['name' => $list['permission'], 'guard_name' => PermissionType::$GUARD_ADMIN])->first()->uuid;
            $list['guard_name'] = PermissionType::$GUARD_ADMIN;

            $routes[] = Arr::except($list, 'permission');
        });

        return $routes;
    }

    private function getDataFromFile($guardName, $type)
    {
        return json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/Route/' . $type . '.json'), true);
    }
}
