<?php

namespace Modules\Base\Contracts;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Modules\Base\Contracts\JWTSubject;
use Modules\Base\Support\JWTAuth\JWTSubjectTrait;

abstract class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword, JWTSubjectTrait;

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;
}
