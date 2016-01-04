<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Wilgucki\Crud\Generators\Model;

class ModelGenerator extends Command
{
    protected $signature = 'crud:model
                            {name : Model name}
                            {--table= : Name of the database table}
                            {--fillable= : List of fillable fileds}
                            {--namespace= : Custom namespace}
                            {--path= : Relative to app directory}';

    protected $description = 'Generates model';

    protected $stub = null;

    public function handle()
    {
        $generator = new Model();
        $generator->setName($this->argument('name'))
            ->setTable($this->option('table'))
            ->setFillable($this->option('fillable'))
            ->setNamespace($this->option('namespace'))
            ->setPath($this->option('path'))
            ->generate();
    }
}
