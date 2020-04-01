<?php

namespace Modules\Admin\Entities;

use Illuminate\Notifications\Notifiable;
use Modules\Base\Contracts\User;
use Modules\Admin\Entities\Traits\CanResetPassword;
use Modules\Permission\Entities\Traits\HasRoles;

class Admin extends User
{
    use Notifiable, CanResetPassword, HasRoles;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'avatar'
    ];

    protected $hidden = [
        'password',
    ];
}
