<?php

namespace Migrations\MigrationsGenerator;

use Illuminate\Support\ServiceProvider;
use Migrations\MigrationsGenerator\Services\DriverSelector;
use Migrations\MigrationsGenerator\Console\GenerateMigrationsCommand;

class MigrationsGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateMigrationsCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->bind(
                DriverSelector::class
            );
        }
    }
}