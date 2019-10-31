<?php

namespace Modules\Route\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Modules\Permission\Entities\PermissionType;
use Modules\Route\Entities\RouteMenu as Menu;

class MenuTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            if ($item['parent_name']){
                $item['parent_uuid'] = Menu::where(['name'=>$item['parent_name'],'guard_name'=>PermissionType::$GUARD_ADMIN])->first()->uuid;
            }
            $item = Arr::except($item, 'parent_name');

            Menu::firstOrCreate(
                ['name' => $item['name'],'guard_name'=>PermissionType::$GUARD_ADMIN],
                $item
            );
        });
    }

    private function getData()
    {
        return $this->getDataFromFile(PermissionType::$GUARD_ADMIN);
    }

    private function getDataFromFile($guardName)
    {
        $list = [];
        $menus = json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/menu.json'), true);

        array_walk($menus, function ($menu) use (&$list, $guardName) {
            $menu['uuid'] = Str::uuid()->getHex();
            $menu['guard_name'] = $guardName;
            $list[] = $menu;
        });

        return $list;
    }
}
