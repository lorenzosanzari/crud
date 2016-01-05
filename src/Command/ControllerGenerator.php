<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Wilgucki\Crud\Generators\Controller;

class ControllerGenerator extends Command
{
    protected $signature = 'crud:controller
                            {name : Name of the controller without Controller suffix}
                            {--namespace= : Use custom namespace in your controller}
                            {--path= : Controller path relative to Controllers dir}
                            {--view-path= : Path of the view files realtive to views directory}
                            {--model= : Model used in your controller}
                            {--with-route : Adds route to routes.php}
                            {--validator= : Validator rules}';

    protected $description = 'Generates resource controller';

    public function handle()
    {
        $generator = new Controller();
        $generator->setName($this->argument('name'))
            ->setNamespace($this->option('namespace'))
            ->setPath($this->option('path'))
            ->setViewPath($this->option('view-path'))
            ->setModel($this->option('model'))
            ->setWithRoute($this->option('with-route'))
            ->setValidator($this->option('validator'))
            ->generate();
    }
}
