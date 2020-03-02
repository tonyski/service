<?php

namespace Modules\Admin\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\Admin\Entities\Traits\CanResetPassword;
use Modules\Permission\Entities\Traits\HasRoles;
use Modules\Route\Entities\Traits\ModelHasRoute;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable, CanResetPassword, HasRoles, ModelHasRoute;

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $fillable = [
        'uuid', 'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
