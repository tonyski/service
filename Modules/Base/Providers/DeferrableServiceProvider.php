<?php
/**
 * Created by PhpStorm.
 * User: fly
 * Date: 2020/3/27
 * Time: 14:42
 */

namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;
use Modules\Base\Services\ListService;
use Modules\Base\Contracts\ListServiceInterface;

class DeferrableServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton(ListServiceInterface::class, function ($app) {
            return new ListService();
        });
    }

    public function provides()
    {
        return [ListServiceInterface::class];
    }
}