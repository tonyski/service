<?php

namespace Modules\Customer\Entities;

use Illuminate\Notifications\Notifiable;
use Modules\Base\Contracts\User;
use Modules\Customer\Entities\Traits\CanResetPassword;

class Customer extends User
{
    use Notifiable, CanResetPassword;

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
