<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class MigrationGenerator extends Command
{
    protected $signature = 'crud:migration
                            {name : Migration name}
                            {--fields= : Fileds populated in database table}';

    protected $description = 'Generates migration file';

    protected $stub = null;

    public function handle()
    {
        $this->getStub();
        $this->setClass();
        $this->setTableName();
        $this->setFields();

        $name = date('Y_m_d_His').'_create_'.str_plural(snake_case($this->argument('name'))).'_table.php';
        file_put_contents(database_path('migrations/'.$name), $this->stub);
    }

    protected function getStub()
    {
        $this->stub = file_get_contents(\Config::get('crud.stub_path').'migration.stub');
    }

    protected function setClass()
    {
        $class = 'Create'.ucfirst(camel_case(str_plural($this->argument('name')))).'Table';
        $this->stub = str_replace('DummyClass', $class, $this->stub);
    }

    protected function setTableName()
    {
        $this->stub = str_replace('DummyTable', str_plural(snake_case($this->argument('name'))), $this->stub);
    }

    protected function setFields()
    {
        $fields = explode(',', $this->option('fields'));
        $write = '';
        foreach ($fields as $field) {
            $fieldData = explode(':', $field);
            $write .= str_repeat(' ', 12)."\$table->{$fieldData[1]}('{$fieldData[0]}');".PHP_EOL;
        }
        $this->stub = str_replace('DummyFields', $write, $this->stub);
    }
}
