<?php

namespace Modules\Permission\Contracts;

use Spatie\Permission\Contracts\Permission as SpatiePermission;

interface Permission extends SpatiePermission
{

    /**
     * Find a permission by its id.
     *
     * @param int $id
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return \Spatie\Permission\Contracts\Permission
     *
     * @deprecated 禁止使用，不在使用id 作为主键,使用findByUuid代替
     */
    public static function findById(int $id, $guardName): SpatiePermission;

    /**
     * Find a permission by its uuid.
     *
     * @param string $uuid
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */
    public static function findByUuid($uuid): SpatiePermission;

}
