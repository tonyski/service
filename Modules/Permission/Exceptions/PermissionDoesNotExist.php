<?php

namespace Modules\Permission\Exceptions;

use InvalidArgumentException;

class PermissionDoesNotExist extends InvalidArgumentException
{
    public static function create(string $permissionName, string $guardName = '')
    {
        return new static("There is no permission named `{$permissionName}` for guard `{$guardName}`.");
    }

    public static function withUuid($uuid)
    {
        return new static("There is no [permission] with id `{$uuid}`.");
    }
}
