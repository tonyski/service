<?php

namespace Modules\Customer\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Modules\Base\Contracts\JWTSubject;
use Modules\Base\Support\JWTAuth\JWTSubjectTrait;
use Modules\Customer\Entities\Traits\CanResetPassword;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable, CanResetPassword, JWTSubjectTrait;

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
