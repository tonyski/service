<?php

namespace Modules\AuthCustomer\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Modules\AuthCustomer\Entities\Traits\CanResetPassword;

class Customer extends Authenticatable implements JWTSubject
{
    use Notifiable,CanResetPassword;

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = [];

    protected $fillable = [
        'uuid','name', 'email', 'password',
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