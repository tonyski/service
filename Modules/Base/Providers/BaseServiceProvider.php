<?php

namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        $this->commands([
            \Modules\Base\Console\SeedInitCommand::class,
        ]);
    }
}
