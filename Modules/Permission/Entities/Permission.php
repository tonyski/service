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
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Modules\Permission\Contracts\Permission as ContractsPermission;
use Modules\Permission\Entities\Traits\HasRoles;
use Modules\Permission\Entities\Traits\RefreshesPermissionCache;
use Modules\Route\Entities\Traits\PermissionToRoute;
use Modules\Base\Support\Locale\LocaleTrait;

class Permission extends SpatiePermission implements ContractsPermission
{
    use HasRoles, RefreshesPermissionCache, PermissionToRoute, LocaleTrait;

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = [];

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

    public static function findByUuId($uuid): PermissionContract
    {
        $permission = static::getPermissions(['uuid' => $uuid])->first();

        if (!$permission) {
            throw PermissionDoesNotExist::withId($uuid);
        }

        return $permission;
    }

}
