<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Wilgucki\Crud\Generators\Migration;

class MigrationGenerator extends Command
{
    protected $signature = 'crud:migration
                            {name : Migration name}
                            {--fields= : Fileds populated in database table}';

    protected $description = 'Generates migration file';

    public function handle()
    {
        $generator = new Migration();
        $generator->setName($this->argument('name'))
            ->setFields($this->option('fields'))
            ->generate();
    }
}
