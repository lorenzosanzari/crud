<?php

namespace Wilgucki\Crud\Generators;

class Controller extends Generator
{
    const STUB = 'controller.stub';
    const STUB_REQUEST = 'request.stub';

    protected $name;
    protected $viewPath;
    protected $model;
    protected $modelClass;
    protected $withRoute;
    protected $validator = null;

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
        } else {
            $this->model = 'App\\'.$this->name;
            $this->modelClass = $this->name;
        }
        return $this;
    }

    public function setWithRoute($withRoute)
    {
        $this->withRoute = $withRoute;
        return $this;
    }

    public function setValidator($validator)
    {
        if ($validator !== null) {
            $entries = explode(',', $validator);
            foreach ($entries as $entry) {
                $rules = explode(':', $entry, 2);
                $this->validator[] = [
                    'field' => $rules[0],
                    'rules' => $rules[1]
                ];
            }
        }
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

    public function getRequestClass()
    {
        if ($this->validator === null) {
            return 'Request';
        } else {
            return 'Save'.$this->name.'Request';
        }
    }

    public function getRequestNamespace()
    {
        if ($this->validator === null) {
            return 'App\Http\Requests';
        } else {
            return 'App\Http\Requests\\'.$this->getRequestClass();
        }
    }

    public function getValidationRules()
    {
        $out = '';
        foreach ($this->validator as $validator) {
            $out .= str_repeat(' ', 12);
            $out .= "'{$validator['field']}' => '{$validator['rules']}',".PHP_EOL;
        }
        return $out;
    }

    public function generate()
    {
        $content = str_replace(
            [
                'DummyNamespace', 'DummyClass', 'DummyView', 'DummyRoute',
                'DummyModelNamespace', 'DummyModelClass', 'DummyRequestNamespace',
                'DummyRequest'
            ],
            [
                $this->getNamespace(), $this->name, $this->getView(),
                snake_case($this->name), $this->model, $this->modelClass,
                $this->getRequestNamespace(), $this->getRequestClass()
            ],
            $this->getStubContent(self::STUB)
        );

        $controllerPath = 'Http/Controllers/';
        if ($this->path !== null) {
            $controllerPath .= $this->path.'/';
        }

        $this->createFile($this->name.'Controller.php', app_path($controllerPath), $content, true);

        if ($this->validator !== null) {
            $this->generateRequest();
        }

        if ($this->withRoute) {
            file_put_contents(
                app_path('Http/routes.php'),
                $this->getResourceRoute(),
                FILE_APPEND
            );
        }
    }

    protected function generateRequest()
    {
        $content = str_replace(
            ['DummyClass', 'DummyRules'],
            [$this->getRequestClass(), $this->getValidationRules()],
            $this->getStubContent(self::STUB_REQUEST)
        );

        $this->createFile(
            $this->getRequestClass().'.php',
            app_path('Http/Requests'),
            $content
        );
    }
}
