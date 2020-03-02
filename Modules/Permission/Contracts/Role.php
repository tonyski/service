<?php

namespace Modules\Permission\Contracts;

use Spatie\Permission\Contracts\Role as SpatieRole;

interface Role extends SpatieRole
{
    /**
     * Find a role by its uuid.
     *
     * @param string $uuid
     * @param string|null $guardName
     *
     * @throws \Modules\Permission\Exceptions\RoleDoesNotExist
     *
     * @return Role
     */
    public static function findByUuid($uuid, $guardName): self ;
}
