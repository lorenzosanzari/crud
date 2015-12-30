<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class ViewGenerator extends Command
{
    protected $signature = 'crud:view
                            {name : Name of the view}
                            {--fields= : List of fields used in views}
                            {--layout= : Name of the layout to extend}
                            {--content-section= : Name of the section used in yield command}
                            {--view-path= : Relative to views directory path where view files will be created}';

    protected $description = 'Generates views for resource controller';

    protected $stubs = [
        'index' => null,
        'form' => null,
        'show' => null
    ];

    protected $fields = [];
    protected $layout;
    protected $path;
    protected $contentSection;
    protected $fieldTypes = [
        'bigInteger' => 'text',
        'char' => 'text',
        'date' => 'text',
        'dateTime' => 'text',
        'decimal' => 'text',
        'double' => 'text',
        'float' => 'text',
        'integer' => 'text',
        'json' => 'textarea',
        'jsonb' => 'textarea',
        'longText' => 'textarea',
        'mediumInteger' => 'text',
        'mediumText' => 'text',
        'smallInteger' => 'text',
        'string' => 'text',
        'text' => 'textarea',
        'time' => 'text',
        'tinyInteger' => 'text',
        'timestamp' => 'text'
    ];

    public function handle()
    {
        $this->layout = $this->option('layout')
            ? $this->option('layout')
            : 'layouts.master';

        $this->contentSection = $this->option('content-section')
            ? $this->option('content-section')
            : 'content';

        $this->path = $this->option('view-path')
            ? DIRECTORY_SEPARATOR.$this->option('view-path').DIRECTORY_SEPARATOR
            : DIRECTORY_SEPARATOR;

        $this->getStubs();
        $this->buildFieldsArray();
        $this->generateIndexView();
        $this->generateShowView();
        $this->generateFormView();

        $viewDir = snake_case($this->argument('name'));

        if (!file_exists(resource_path('views'.$this->path.$viewDir))) {
            mkdir(resource_path('views'.$this->path.$viewDir), 0777, true);
        }

        $index = resource_path('views'.$this->path.$viewDir.'/index.blade.php');
        file_put_contents($index, $this->stubs['index']);

        $show = resource_path('views'.$this->path.$viewDir.'/show.blade.php');
        file_put_contents($show, $this->stubs['show']);

        $form = resource_path('views'.$this->path.$viewDir.'/form.blade.php');
        file_put_contents($form, $this->stubs['form']);
    }

    protected function getStubs()
    {
        $this->stubs['index'] = file_get_contents(\Config::get('crud.stub_path').'index.blade.stub');
        $this->stubs['form'] = file_get_contents(\Config::get('crud.stub_path').'form.blade.stub');
        $this->stubs['show'] = file_get_contents(\Config::get('crud.stub_path').'show.blade.stub');
    }

    protected function buildFieldsArray()
    {
        $fields = explode(',', $this->option('fields'));
        foreach ($fields as $field) {
            $fieldData = explode(':', $field);
            $this->fields[] = [
                'name' => $fieldData[0],
                'type' => $fieldData[1]
            ];
        }
    }

    protected function generateIndexView()
    {
        $this->stubs['index'] = str_replace(
            ['DummyHeader', 'DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getIndexHeader(), $this->getIndexRow(), snake_case($this->argument('name')),
                $this->layout, $this->contentSection],
            $this->stubs['index']
        );
    }

    protected function generateShowView()
    {
        $this->stubs['show'] = str_replace(
            ['DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getShowRow(), snake_case($this->argument('name')), $this->layout, $this->contentSection],
            $this->stubs['show']
        );
    }

    protected function generateFormView()
    {
        $this->stubs['form'] = str_replace(
            ['DummyFieldset', 'DummyLayout', 'DummySection'],
            [$this->getFormFieldset(), $this->layout, $this->contentSection],
            $this->stubs['form']
        );
    }

    protected function getIndexHeader()
    {
        $header = '';
        foreach ($this->fields as $field) {
            $header .= str_repeat(' ', 12);
            $header .= '<td>'.ucwords(str_replace('_', ' ', $field['name'])).'</td>'.PHP_EOL;
        }
        return $header;
    }

    protected function getIndexRow()
    {
        $row = '';
        foreach ($this->fields as $field) {
            $row .= str_repeat(' ', 16).'<td>{{$item->'.$field['name'].'}}</td>'.PHP_EOL;
        }
        return $row;
    }

    protected function getShowRow()
    {
        $row = '';
        foreach ($this->fields as $field) {
            $row .= str_repeat(' ', 12).'<tr>'.PHP_EOL;
            $row .= str_repeat(' ', 16).'<td>'. ucwords(str_replace('_', ' ', $field['name'])).'</td>'.PHP_EOL;
            $row .= str_repeat(' ', 16).'<td>{{$item->'.$field['name'].'}}</td>'.PHP_EOL;
            $row .= str_repeat(' ', 12).'</tr>'.PHP_EOL;
        }
        return $row;
    }

    protected function getFormFieldset()
    {
        $row = '';
        foreach ($this->fields as $field) {
            $row .= str_repeat(' ', 8).'<fieldset>'.PHP_EOL;
            $row .= str_repeat(' ', 12).'{!! Form::label(\''.$field['name'].'\') !!}'.PHP_EOL;
            $row .= str_repeat(' ', 12).$this->getField($field['name'], $field['type']).PHP_EOL;
            $row .= str_repeat(' ', 8).'</fieldset>'.PHP_EOL;
        }
        return $row;
    }

    protected function getFormFieldType($type)
    {
        return $this->fieldTypes[$type];
    }

    protected function getField($name, $type)
    {
        switch ($type) {
            case 'boolean':
                $formField  = '<label>{!! Form::radio(\''.$name.'\', 0, true) !!} No</label>';
                $formField .= '<label>{!! Form::radio(\''.$name.'\', 1) !!} Yes</label>';
                break;
            default:
                $formField = '{!! Form::'.$this->getFormFieldType($type).'(\''.$name.'\') !!}';
        }
        return $formField;
    }
}
