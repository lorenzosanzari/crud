<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class ModelGenerator extends Command
{
    protected $signature = 'crud:model
                            {name : Model name}
                            {--table= : Name of the database table}
                            {--fillable= : List of fillable fileds}
                            {--namespace= : Custom namespace}';

    protected $description = 'Generates model';

    protected $stub = null;

    public function handle()
    {
        $this->getStub();
        $this->setNamespace();
        $this->setClass();
        $this->setTableName();
        $this->setFillable();

        file_put_contents(app_path($this->argument('name').'.php'), $this->stub);
    }

    protected function getStub()
    {
        $this->stub = file_get_contents(\Config::get('crud.stub_path').'model.stub');
    }

    protected function setNamespace()
    {
        $namespace = $this->option('namespace')
            ? $this->option('namespace')
            : 'App';

        $this->stub = str_replace('DummyNamespace', $namespace, $this->stub);
    }

    protected function setClass()
    {
        $this->stub = str_replace('DummyClass', $this->argument('name'), $this->stub);
    }

    protected function setTableName()
    {
        $table = $this->option('table')
            ? $this->option('table')
            : str_plural(snake_case($this->argument('name')));

        $this->stub = str_replace('DummyTable', $table, $this->stub);
    }

    protected function setFillable()
    {
        $fillable = [];
        if ($this->option('fillable') !== null) {
            $fs = explode(',', $this->option('fillable'));
            foreach ($fs as $f) {
                $fillable[] = "'".trim($f)."'";
            }
        }

        $this->stub = str_replace('DummyFillable', implode(', ', $fillable), $this->stub);
    }
}
