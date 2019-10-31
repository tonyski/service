<?php

namespace Modules\Permission\Entities\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasPermissions as SpatieHasPermissions;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Contracts\Permission;
use Modules\Permission\Entities\PermissionType;

trait HasPermissions
{
    use SpatieHasPermissions;
    
    private $indexPermission = null; // 用户的首页权限

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            'permission_uuid'
        )->withPivot('expires_at');
    }

    /**
     * Scope the model query to certain permissions only.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePermission(Builder $query, $permissions): Builder
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $rolesWithPermissions = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->where(function ($query) use ($permissions, $rolesWithPermissions) {
            $query->whereHas('permissions', function ($query) use ($permissions) {
                $query->where(function ($query) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $query->orWhere(config('permission.table_names.permissions') . '.uuid', $permission->uuid);
                    }
                });
            });
            if (count($rolesWithPermissions) > 0) {
                $query->orWhereHas('roles', function ($query) use ($rolesWithPermissions) {
                    $query->where(function ($query) use ($rolesWithPermissions) {
                        foreach ($rolesWithPermissions as $role) {
                            $query->orWhere(config('permission.table_names.roles') . '.uuid', $role->uuid);
                        }
                    });
                });
            }
        });
    }

    /**
     * Determine if the model may perform the given permission.
     *
     * @param string|int|\Spatie\Permission\Contracts\Permission $permission
     * @param string|null $guardName
     *
     * @return bool
     * @throws PermissionDoesNotExist
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName(
                $permission,
                $guardName ?? $this->getDefaultGuardName()
            );
        }

        if (!$permission instanceof Permission) {
            throw new PermissionDoesNotExist;
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    public function hasDirectPermission($permission): bool
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
            if (!$permission) {
                return false;
            }
        }

        if (!$permission instanceof Permission) {
            return false;
        }

        return $this->permissions->contains('uuid', $permission->uuid);
    }

    /**
     * Grant the given permission(s) to a role.
     *
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return $this
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter(function ($permission) {
                return $permission instanceof Permission;
            })
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })
            ->map->uuid
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->permissions()->sync($permissions, false);
            $model->load('permissions');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($permissions, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->permissions()->sync($permissions, false);
                    $object->load('permissions');
                    $modelLastFiredOn = $object;
                }
            );
        }

        $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     *
     * @return \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Permission[]|\Illuminate\Support\Collection
     */
    protected function getStoredPermission($permissions)
    {
        $permissionClass = $this->getPermissionClass();

        if (is_string($permissions)) {
            return $permissionClass->findByName($permissions, $this->getDefaultGuardName());
        }

        if (is_array($permissions)) {
            return $permissionClass
                ->whereIn('name', $permissions)
                ->whereIn('guard_name', $this->getGuardNames())
                ->get();
        }

        return $permissions;
    }

    /**
     * 返回首页权限
     */
    public function getIndexPermissions()
    {
        if (is_null($this->indexPermission)) {
            $defaultRole = $this->roles()->where('is_default', 1)->first();

            if (!is_null($defaultRole)) {
                $this->indexPermission = $defaultRole->permissions()->where('type', PermissionType::$PERMISSION_INDEX)->first();
            }
        }

        return $this->indexPermission;
    }

    /**
     * 返回所有的入口权限
     */
    public function getRoutePermissions(): Collection
    {
        $permissions = $this->getAllPermissions();

        return $permissions->filter(function ($permission, $key) {
            return $permission->type == PermissionType::$PERMISSION_ROUTE;
        });

        /*
        $relationships = [
            'permissions' => function ($query) {
                $query->where('type', PermissionType::$PERMISSION_ROUTE);
            },
            'roles',
            'roles.permissions' => function ($query) {
                $query->where('type', PermissionType::$PERMISSION_ROUTE);
            }];
        $this->load($relationships);

        $permissions = $this->permissions;

        if ($this->roles) {
            $rolesPermissions = $this->roles->flatMap(function ($role) {
                return $role->permissions;
            })->sort()->values();

            $permissions = $permissions->merge($rolesPermissions);
        }

        return $permissions->sort()->values();
        */
    }

}
