<?php

namespace Modules\Permission\Entities\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Permission\Traits\HasRoles as SpatieHasRoles;
use Spatie\Permission\Contracts\Role;

trait HasRoles
{
    use SpatieHasRoles;

    /**
     * A model may have multiple roles.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'role_uuid'
        )->withPivot('is_default');
    }

    public function scopeRole(Builder $query, $roles, $guard = null): Builder
    {
        if ($roles instanceof Collection) {
            $roles = $roles->all();
        }

        if (! is_array($roles)) {
            $roles = [$roles];
        }

        $roles = array_map(function ($role) use ($guard) {
            if ($role instanceof Role) {
                return $role;
            }

//            $method = is_numeric($role) ? 'findById' : 'findByName';
            $method = 'findByName';
            $guard = $guard ?: $this->getDefaultGuardName();

            return $this->getRoleClass()->{$method}($role, $guard);
        }, $roles);

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere(config('permission.table_names.roles').'.uuid', $role->uuid);
                }
            });
        });
    }

    public function assignRole(...$roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (empty($role)) {
                    return false;
                }

                return $this->getStoredRole($role);
            })
            ->filter(function ($role) {
                return $role instanceof Role;
            })
            ->each(function ($role) {
                $this->ensureModelSharesGuard($role);
            })
            ->map->uuid
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->roles()->sync($roles, false);
            $model->load('roles');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($roles, $model) {
                    static $modelLastFiredOn;
                    if ($modelLastFiredOn !== null && $modelLastFiredOn === $model) {
                        return;
                    }
                    $object->roles()->sync($roles, false);
                    $object->load('roles');
                    $modelLastFiredOn = $object;
                });
        }

        $this->forgetCachedPermissions();

        return $this;
    }

    public function hasRole($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('uuid', $roles->uuid);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    public function hasAllRoles($roles): bool
    {
        if (is_string($roles) && false !== strpos($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if ($roles instanceof Role) {
            return $this->roles->contains('uuid', $roles->uuid);
        }

        $roles = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $roles->intersect($this->getRoleNames()) == $roles;
    }

    protected function getStoredRole($role): Role
    {
        $roleClass = $this->getRoleClass();

        if (is_string($role)) {
            return $roleClass->findByName($role, $this->getDefaultGuardName());
        }

        return $role;
    }
}
