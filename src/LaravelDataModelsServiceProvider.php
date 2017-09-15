<?php

namespace oleglfed\DataModel;

use Illuminate\Support\ServiceProvider;
use oleglfed\DataModel\Commands\GenerateDomain;

class LaravelDataModelsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the API doc commands.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateDomain::class,
            ]);
        }
    }
}
