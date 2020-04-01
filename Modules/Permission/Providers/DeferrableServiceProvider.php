<?php

namespace Modules\Permission\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Modules\Permission\Contracts\PermissionService as ContractService;
use Modules\Permission\Services\PermissionService;

class DeferrableServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(ContractService::class, function ($app) {
            return new PermissionService();
        });
    }

    public function provides()
    {
        return [ContractService::class];
    }
}