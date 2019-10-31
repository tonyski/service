<?php

namespace Modules\Permission\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Permission\Entities\Role;
use Modules\Permission\Entities\PermissionType;

class RoleTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            Role::firstOrCreate(
                ['name' => $item['name'], 'guard_name' => PermissionType::$GUARD_ADMIN],
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
        $group = json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/role.json'), true);

        array_walk($group, function ($roles) use (&$list, $guardName) {
            array_walk($roles, function ($role) use (&$list, $guardName) {
                $role['uuid'] = Str::uuid()->getHex();
                $role['guard_name'] = $guardName;
                $list[] = $role;
            });
        });

        return $list;
    }
}
