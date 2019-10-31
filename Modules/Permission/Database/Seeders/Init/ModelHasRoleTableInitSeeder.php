<?php

namespace Modules\Permission\Database\Seeders\Init;

use Illuminate\Database\Seeder;
use Modules\Admin\Entities\Admin;
use Modules\Permission\Entities\Role;
use Modules\Permission\Entities\PermissionType;

class ModelHasRoleTableInitSeeder extends Seeder
{
    public function run()
    {
        collect($this->getData())->each(function ($item) {
            if (is_array($item['role'])) {
                $admin = Admin::where('name', $item['admin'])->first();
                $roles = Role::where('guard_name', PermissionType::$GUARD_ADMIN)
                    ->whereIn('name', $item['role'])
                    ->get();
                $admin->syncRoles($roles);
            }
        });
    }

    private function getData()
    {
        return $this->getDataFromFile(PermissionType::$GUARD_ADMIN);
    }

    private function getDataFromFile($guardName)
    {
        return json_decode(file_get_contents(__DIR__ . '/Data/' . ucfirst($guardName) . '/model_has_role.json'), true);
    }
}
