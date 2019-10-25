<?php

namespace Modules\Permission\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Permission\Entities\Permission;

class PermissionTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            Permission::firstOrCreate(
                ['name' => $item['name'],'guard_name' => Permission::GUARD_ADMIN],
                $item
            );
        });
    }

    private function getData()
    {
        $adminFeature = $this->getDataFromFile(Permission::GUARD_ADMIN, Permission::PERMISSION_FEATURE, 'feature');
        $adminRoute = $this->getDataFromFile(Permission::GUARD_ADMIN, Permission::PERMISSION_ROUTE, 'route');
        $adminIndex = $this->getDataFromFile(Permission::GUARD_ADMIN, Permission::PERMISSION_INDEX, 'index');

        return array_merge($adminFeature, $adminRoute, $adminIndex);
    }

    private function getDataFromFile($guardName, $permissionType, $permissionName)
    {
        $list = [];
        $group = json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/permission/' . $permissionName . '.json'), true);

        array_walk($group, function ($permissions) use (&$list, $guardName, $permissionType) {
            array_walk($permissions, function ($permission) use (&$list, $guardName, $permissionType) {
                $permission['uuid'] = Str::uuid()->getHex();
                $permission['guard_name'] = $guardName;
                $permission['type'] = $permissionType;

                $list[] = $permission;
            });
        });

        return $list;
    }
}
