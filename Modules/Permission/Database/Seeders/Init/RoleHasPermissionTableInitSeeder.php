<?php

namespace Modules\Permission\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Modules\Permission\Entities\Role;
use Modules\Permission\Entities\Permission;
use Modules\Permission\Entities\PermissionType;

class RoleHasPermissionTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            //角色对应的首页权限只能有一个

            //超管权限
            if (is_string($item['permission'])) {
                if ($item['permission'] === 'all') {
                    $role = Role::findByName($item['role'], PermissionType::$GUARD_ADMIN);

                    $permissions = Permission::where('guard_name', PermissionType::$GUARD_ADMIN)
                        ->where('type', '<>', PermissionType::$PERMISSION_INDEX)
                        ->get()->map->uuid->all();
                    $index = Permission::where('guard_name', PermissionType::$GUARD_ADMIN)
                        ->where('name', $item['index'])
                        ->first();
                    $permissions[] = $index->uuid;

                    $role->syncPermissions($permissions);
                }
            }

            if (is_array($item['permission'])) {
                $role = Role::findByName($item['role'], PermissionType::$GUARD_ADMIN);
                $permissions = Permission::where('guard_name', PermissionType::$GUARD_ADMIN)
                    ->whereIn('name', $item['permission'])
                    ->get()->map->uuid->all();
                $role->syncPermissions($permissions);
            }
        });
    }

    private function getData()
    {
        return $this->getDataFromFile(PermissionType::$GUARD_ADMIN);
    }

    private function getDataFromFile($guardName)
    {
        return json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/role_has_permission.json'), true);
    }
}
