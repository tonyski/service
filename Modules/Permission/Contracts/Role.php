<?php

namespace Modules\Permission\Contracts;

use Spatie\Permission\Contracts\Role as SpatieRole;

interface Role extends SpatieRole
{
    /**
     * Find a role by its id and guard name.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     *
     * @deprecated 禁止使用，不在使用id 作为主键,使用findByUuid代替
     */
    public static function findById(int $id, $guardName): SpatieRole;

    /**
     * Find a role by its uuid and guard name.
     *
     * @param string $uuid
     *
     * @return Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByUuId($uuid): SpatieRole;
}
