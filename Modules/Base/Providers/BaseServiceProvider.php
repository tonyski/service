<?php

namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Base\Console\SeedInitCommand;
use Modules\Base\Services\ListService;
use Modules\Base\Contracts\ListServiceInterface;

class BaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ListServiceInterface::class, function ($app) {
            return new ListService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedInitCommand::class,
            ]);
        }

        $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'base');
    }
}
