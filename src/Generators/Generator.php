<?php

namespace Wilgucki\Crud\Generators;

abstract class Generator
{
    protected $name;
    protected $fields;
    protected $class;
    protected $namespace;
    protected $path;

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setFields($fields)
    {
        foreach (explode(',', $fields) as $field) {
            $fieldData = explode(':', $field);
            $this->fields[] = [
                'name' => $fieldData[0],
                'type' => $fieldData[1]
            ];
        }
        return $this;
    }

    public function setNamespace($namespace)
    {
        if ($namespace) {
            $this->namespace = $namespace;
        }
        return $this;
    }

    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    protected function getStubContent($stub)
    {
        return file_get_contents(\Config::get('crud.stub_path').$stub);
    }

    protected function createFile($name, $path, $content, $createDir = false)
    {
        if ($createDir) {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        file_put_contents($path.DIRECTORY_SEPARATOR.$name, $content);
    }

    abstract public function generate();
}
