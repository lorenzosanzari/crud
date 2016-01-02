<?php

namespace Wilgucki\Crud\Generators;

class Model extends Generator
{
    const STUB = 'model.stub';

    protected $fillable;
    protected $table;


    public function setFillable($fillable)
    {
        $this->fillable = $fillable;
        return $this;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getNamespace()
    {
        return $this->namespace ? $this->namespace : 'App';
    }

    public function getTable()
    {
        return $this->table ? $this->table : str_plural(snake_case($this->name));
    }

    public function getFillable()
    {
        $fillable = [];
        if ($this->fillable !== null) {
            $fs = explode(',', $this->fillable);
            foreach ($fs as $f) {
                $fillable[] = "'".trim($f)."'";
            }
        }
        return implode(', ', $fillable);
    }

    public function generate()
    {
        $content = str_replace(
            ['DummyNamespace', 'DummyClass', 'DummyTable', 'DummyFillable'],
            [$this->getNamespace(), $this->name, $this->getTable(), $this->getFillable()],
            $this->getStubContent(self::STUB)
        );

        $this->createFile($this->name.'.php', app_path($this->path), $content, true);
    }
}
