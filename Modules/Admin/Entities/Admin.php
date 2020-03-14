<?php

namespace Modules\Admin\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Base\Contracts\JWTSubject;
use Modules\Base\Support\JWTAuth\JWTSubjectTrait;
use Modules\Admin\Entities\Traits\CanResetPassword;
use Modules\Permission\Entities\Traits\HasRoles;
use Modules\Route\Entities\Traits\ModelHasRoute;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable, CanResetPassword, HasRoles, ModelHasRoute, JWTSubjectTrait;

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'avatar'
    ];

    protected $hidden = [
        'password',
    ];
}
