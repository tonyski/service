<?php
/**
 * Created by PhpStorm.
 * User: fly.fei
 * Date: 2019/10/18
 * Time: 16:05
 */

namespace Modules\Permission\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Guard;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Modules\Permission\Contracts\Permission as ContractsPermission;
use Modules\Permission\Entities\Traits\HasRoles;
use Modules\Permission\Entities\Traits\RefreshesPermissionCache;

class Permission extends SpatiePermission implements ContractsPermission
{
    use HasRoles;
    use RefreshesPermissionCache;

    const GUARD_ADMIN = 'admin';      // 使用的 guard
    const GUARD_CUSTOMER = 'customer';// 使用的 guard
    const PERMISSION_FEATURE = 1;     // 功能权限
    const PERMISSION_ROUTE = 2;       // 访问入口权限
    const PERMISSION_INDEX = 3;       // 访问首页权限

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = ['uuid'];

    protected $casts = [
        'locale' => 'json',
    ];

    /**
     * A permission can be applied to roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'permission_uuid',
            'role_uuid'
        );
    }

    /**
     * A permission belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_permissions'),
            'permission_uuid',
            config('permission.column_names.model_morph_key')
        )->withPivot('expires_at');
    }

    public static function findByUuId($uuid, $guardName = null): PermissionContract
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);
        $permission = static::getPermissions(['uuid' => $uuid, 'guard_name' => $guardName])->first();

        if (! $permission) {
            throw PermissionDoesNotExist::withId($uuid, $guardName);
        }

        return $permission;
    }

}
