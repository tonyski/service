<?php

namespace Modules\Route\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Modules\Route\Contracts\RouteService;
use Modules\Route\Services\MenuRouteTreeService;

use Modules\Route\Contracts\Services\MenuService as MenuServiceContract;
use Modules\Route\Services\MenuService;
use Modules\Route\Contracts\Repositories\MenuRepository as MenuRepositoryContract;
use Modules\Route\Repositories\MenuRepository;

class DeferrableServiceProvider extends ServiceProvider implements DeferrableProvider
{

    public $bindings = [
        MenuServiceContract::class => MenuService::class,
        MenuRepositoryContract::class => MenuRepository::class,
    ];

    public $singletons = [
        RouteService::class => MenuRouteTreeService::class,
    ];

    public function provides()
    {
        return [
            RouteService::class,
            MenuServiceContract::class,
            MenuRepositoryContract::class
        ];
    }
}