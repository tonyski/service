<?php

namespace Modules\Permission\Contracts;

use Spatie\Permission\Contracts\Permission as SpatiePermission;

interface Permission extends SpatiePermission
{
    /**
     * Find a permission by its uuid.
     *
     * @param string $uuid
     * @param string|null $guardName
     *
     * @throws \Modules\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */
    public static function findByUuid($uuid, $guardName): self;
}
