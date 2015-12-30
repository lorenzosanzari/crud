<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class ControllerGenerator extends Command
{
    protected $signature = 'crud:controller
                            {name : Name of the controller without Controller suffix}
                            {--namespace= : Use custom namespace in your controller}
                            {--path= : Controller path relative to Controllers dir}
                            {--model= : Model name used in your controller}
                            {--with-route : Adds route to routes.php}';

    protected $description = 'Generates resource controller';

    protected $stub = null;

    public function handle()
    {
        $this->getStub();
        $this->setNamespace();
        $this->setClass();
        $this->setModelName();
        $this->setViewName();

        $path = $this->option('path');
        $controllerPath = 'Http/Controllers/';
        if ($path !== null) {
            $controllerPath .= $path.'/';
        }

        if (!file_exists(app_path($controllerPath))) {
            mkdir(app_path($controllerPath), 0777, true);
        }

        file_put_contents(app_path($controllerPath.$this->argument('name').'Controller.php'), $this->stub);

        if ($this->option('with-route')) {
            $route = $this->getRoute();
            file_put_contents(app_path('Http/routes.php'), $route, FILE_APPEND);
        }
    }

    protected function getStub()
    {
        $this->stub = file_get_contents(\Config::get('crud.stub_path').'controller.stub');
    }

    protected function setNamespace()
    {
        $namespace = $this->option('namespace')
            ? '\\'.$this->option('namespace')
            : '';

        $this->stub = str_replace('DummyNamespace', $namespace, $this->stub);
    }

    protected function setClass()
    {
        $this->stub = str_replace('DummyClass', $this->argument('name'), $this->stub);
    }

    protected function setModelName()
    {
        $model = $this->option('model')
            ? $this->option('model')
            : $this->argument('name');

        $this->stub = str_replace('DummyModel', $model, $this->stub);
    }

    protected function setViewName()
    {
        $model = $this->option('model')
            ? $this->option('model')
            : $this->argument('name');

        $view = snake_case($model);

        $this->stub = str_replace('DummyView', $view, $this->stub);
    }

    protected function getRoute()
    {
        $route = snake_case($this->argument('name'));
        $controller = ($this->option('namespace') !== null ? $this->option('namespace').'\\' : '')
            .$this->argument('name').'Controller';
        return "Route::resource('{$route}', '{$controller}');".PHP_EOL;
    }
}
