<?php

namespace Migrations\MigrationsGenerator;

use Illuminate\Support\ServiceProvider;
use Migrations\MigrationsGenerator\Console\GenerateMigrationsCommand;

class MigrationsGeneratorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateMigrationsCommand::class,
            ]);
        }
    }

    public function register()
    {
        // Aqui você pode mesclar configurações ou registrar bindings se necessário.
        if ($this->app->environment() !== 'production') {

        }
    }
}