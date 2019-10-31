<?php

namespace Modules\Route\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Base\Support\Locale\LocaleTrait;

class RouteMenu extends Model
{
    use LocaleTrait;

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
        )->withPivot('sort')->as('menu_route');
    }

    public function parent()
    {
        return $this->hasOne(RouteMenu::class, 'uuid', 'parent_uuid');
    }

    public function children()
    {
        return $this->hasMany(RouteMenu::class, 'parent_uuid', 'uuid');
    }

    public static function allMenuTree(): Collection
    {
        return RouteMenu::where('parent_uuid', '')->get()->each(function ($item) {
            $item->menuTree();
        });
    }

    public function menuTree()
    {
        if ($this->children->count()) {
            $this->children->each(function ($item) {
                $item->menuTree();
            });
        }
    }

    public static function allMenuRouteTree(): Collection
    {
        $menus = RouteMenu::where('parent_uuid', '')->get();
        $menus->loadMissing('routes');

        $menus->each(function ($item) {
            $item->menuRouteTree();
        });

        return $menus;
    }

    public function menuRouteTree()
    {
        if ($this->children->count()) {
            $this->children->loadMissing('routes');
            $this->children->each(function ($item) {
                $item->menuRouteTree();
            });
        }
    }
}
