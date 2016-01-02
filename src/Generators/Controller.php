<?php

namespace Wilgucki\Crud\Generators;

class Controller extends Generator
{
    const STUB = 'controller.stub';

    protected $name;
    protected $viewPath;
    protected $model;
    protected $modelClass;
    protected $withRoute;

    public function setNamespace($namespace)
    {
        if ($namespace) {
            $this->namespace = $namespace;
        }
        return $this;
    }

    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
        return $this;
    }

    public function setModel($model)
    {
        if ($model !== null) {
            $this->model = $model;
            $rc = new \ReflectionClass($model);
            $this->modelClass = $rc->getShortName();
        }
        return $this;
    }

    public function setWithRoute($withRoute)
    {
        $this->withRoute = $withRoute;
        return $this;
    }

    public function getNamespace()
    {
        if ($this->namespace) {
            return '\\'.$this->namespace;
        } else {
            return '';
        }
    }

    public function getView()
    {
        $view = snake_case($this->name);
        if ($this->viewPath) {
            $view = str_replace('/', '.', $this->viewPath) . '.' . $view;
        }
        return $view;
    }

    protected function getResourceRoute()
    {
        $route = snake_case($this->name);
        $controller = ($this->namespace != '' ? $this->namespace.'\\' : '').$this->name;
        return "Route::resource('{$route}', '{$controller}Controller');".PHP_EOL;
    }

    public function getModelClass()
    {
        if ($this->model) {
            return $this->model;
        }
        return $this->name;
    }

    public function generate()
    {
        $content = str_replace(
            ['DummyNamespace', 'DummyClass', 'DummyView', 'DummyRoute', 'DummyModelNamespace', 'DummyModelClass'],
            [$this->getNamespace(), $this->name, $this->getView(), snake_case($this->name),
                $this->model, $this->modelClass],
            $this->getStubContent(self::STUB)
        );

        $controllerPath = 'Http/Controllers/';
        if ($this->path !== null) {
            $controllerPath .= $this->path.'/';
        }

        $this->createFile($this->name.'Controller.php', app_path($controllerPath), $content, true);

        if ($this->withRoute) {
            file_put_contents(
                app_path('Http/routes.php'),
                $this->getResourceRoute(),
                FILE_APPEND
            );
        }
    }
}
