<?php

namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Base\Console\SeedInitCommand;

class BaseServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedInitCommand::class,
            ]);
        }

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'base');
    }
}
