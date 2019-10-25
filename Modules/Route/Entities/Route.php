<?php

namespace Modules\Route\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Route extends Model
{
    const GUARD_ADMIN = 'admin';      // 使用的 guard
    const GUARD_CUSTOMER = 'customer';// 使用的 guard

    protected $primaryKey = 'uuid';

    protected $keyType = 'char';

    public $incrementing = false;

    protected $guarded = ['uuid'];

    protected $casts = [
        'locale' => 'json',
    ];

    /**
     * 一个入口可以对应多个分类
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            RouteMenu::class,
            'route_to_menus',
            'route_uuid',
            'route_menu_uuid'
        )->withPivot('sort');
    }

    /**
     * 一个入口就是一个权限
     */
    public function permission()
    {
        return $this->belongsTo('Modules\Permission\Entities\Permission','uuid','uuid');
    }
}
