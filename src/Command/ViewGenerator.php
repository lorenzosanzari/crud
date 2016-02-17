<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Wilgucki\Crud\Generators\View;

class ViewGenerator extends Command
{
    protected $signature = 'crud:view
                            {name : Name of the view}
                            {--fields= : List of fields used in views}
                            {--layout= : Name of the layout to extend}
                            {--content-section= : Name of the section used in yield command}
                            {--path= : Relative to views directory path where view files will be created}
                            {--theme= : View theme}';

    protected $description = 'Generates views for resource controller';

    public function handle()
    {
        $generator = new View();
        $generator->setName($this->argument('name'))
            ->setFields($this->option('fields'))
            ->setLayout($this->option('layout'))
            ->setContentSection($this->option('content-section'))
            ->setPath($this->option('path'))
            ->setTheme($this->option('theme'))
            ->generate();
    }
}
