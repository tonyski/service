<?php

namespace Modules\Permission\Services;

use Modules\Permission\Contracts\PermissionService as ContractService;

class PermissionService implements ContractService
{
    public function syncUserRoles($user, $roles, $defaultRole = '')
    {
        $pivot = [];
        foreach ($roles as $r) {
            $pivot[$r] = ['is_default' => $r == $defaultRole ? 1 : 0];
        }

        return $user->roles()->sync($pivot);
    }

    public function syncUserPermissions($user, $permissions)
    {
        return $user->permissions()->sync($permissions);
    }

    public function getUserIndexPermission($user)
    {
        return $user->getDefaultIndexPermission();
    }

    public function getUserRoutePermission($user)
    {
        return $user->getRoutePermissions();
    }


}