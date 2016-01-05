<?php

namespace Wilgucki\Crud;

use Illuminate\Support\ServiceProvider;
use Wilgucki\Crud\Command\ControllerGenerator;
use Wilgucki\Crud\Command\CrudFromDbGenerator;
use Wilgucki\Crud\Command\CrudFromFileGenerator;
use Wilgucki\Crud\Command\CrudGenerator;
use Wilgucki\Crud\Command\MigrationGenerator;
use Wilgucki\Crud\Command\ModelGenerator;
use Wilgucki\Crud\Command\ViewGenerator;

class CrudServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/crud.php' => config_path('crud.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../config/generator.php' => resource_path('crud/generator.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../stubs/' => resource_path('crud/stubs/'),
        ], 'stub');
    }

    public function register()
    {
        $this->app['command.crud.model'] = $this->app->share(
            function ($app) {
                return new ModelGenerator();
            }
        );

        $this->app['command.crud.controller'] = $this->app->share(
            function ($app) {
                return new ControllerGenerator();
            }
        );

        $this->app['command.crud.migration'] = $this->app->share(
            function ($app) {
                return new MigrationGenerator();
            }
        );

        $this->app['command.crud.view'] = $this->app->share(
            function ($app) {
                return new ViewGenerator();
            }
        );

        $this->app['command.crud.generate'] = $this->app->share(
            function ($app) {
                return new CrudGenerator();
            }
        );

        $this->app['command.crud.from-database'] = $this->app->share(
            function ($app) {
                return new CrudFromDbGenerator();
            }
        );

        $this->app['command.crud.from-file'] = $this->app->share(
            function ($app) {
                return new CrudFromFileGenerator();
            }
        );

        $this->commands('command.crud.model');
        $this->commands('command.crud.controller');
        $this->commands('command.crud.migration');
        $this->commands('command.crud.view');
        $this->commands('command.crud.generate');
        $this->commands('command.crud.from-database');
        $this->commands('command.crud.from-file');
    }
}
