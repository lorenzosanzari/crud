<?php

namespace Wilgucki\Crud\Generators;

class Migration extends Generator
{
    const STUB = 'migration.stub';

    protected $fields;
    protected $name;

    public function getFileds()
    {
        $out = '';
        foreach ($this->fields as $field) {
            $out .= str_repeat(' ', 12)."\$table->{$field['type']}('{$field['name']}');".PHP_EOL;
        }
        return $out;
    }

    public function generate()
    {
        $content = str_replace(
            ['DummyClass', 'DummyTable', 'DummyFields'],
            [str_plural($this->name), str_plural(snake_case($this->name)), $this->getFileds()],
            $this->getStubContent(self::STUB)
        );

        $file = date('Y_m_d_His').'_create_'.str_plural(snake_case($this->name)).'_table.php';
        $this->createFile($file, database_path('migrations'), $content);
    }
}
