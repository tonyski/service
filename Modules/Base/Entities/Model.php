<?php

namespace Modules\Base\Entities;

use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model extends LaravelModel
{
    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = [];
}
