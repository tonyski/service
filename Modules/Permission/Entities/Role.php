<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/10/18
 * Time: 18:11
 */

namespace Modules\Permission\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Guard;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Models\Role as SpatieRole;
use Modules\Permission\Contracts\Role as ContractsRole;
use Modules\Permission\Entities\Traits\HasPermissions;
use Modules\Permission\Entities\Traits\RefreshesPermissionCache;

class Role extends SpatieRole implements ContractsRole
{
    use HasPermissions;
    use RefreshesPermissionCache;

    const GUARD_ADMIN = 'admin';      // 使用的 guard
    const GUARD_CUSTOMER = 'customer';// 使用的 guard

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = ['uuid'];

    protected $casts = [
        'locale' => 'json',
    ];

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_uuid',
            'permission_uuid'
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'role_uuid',
            config('permission.column_names.model_morph_key')
        )->withPivot('is_default');
    }

    public static function findByUuId($uuid, $guardName = null): RoleContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::where('uuid', $uuid)->where('guard_name', $guardName)->first();

        if (!$role) {
            throw RoleDoesNotExist::withId($uuid);
        }

        return $role;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param string|Permission $permission
     *
     * @return bool
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    public function hasPermissionTo($permission): bool
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (!$this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains('uuid', $permission->uuid);
    }
}
