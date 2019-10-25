<?php

namespace Modules\Route\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RouteMenu extends Model
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
     * 一个分类可以对应多个入口
     */
    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(
            Route::class,
            'route_to_menus',
            'route_menu_uuid',
            'route_uuid'
        )->withPivot('sort');
    }
}
