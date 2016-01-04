<?php

namespace Wilgucki\Crud\Generators;

class View extends Generator
{
    const STUB_LAYOUT = 'master.blade.stub';
    const STUB_INDEX = 'index.blade.stub';
    const STUB_SHOW = 'show.blade.stub';
    const STUB_FORM = 'form.blade.stub';

    protected $layout = 'layouts.master';
    protected $contentSection = 'content';

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

    public function setLayout($layout)
    {
        if ($layout) {
            $this->layout = $layout;
        }
        return $this;
    }

    public function setContentSection($contentSection)
    {
        if ($contentSection) {
            $this->contentSection = $contentSection;
        }
        return $this;
    }

    public function setPath($path)
    {
        if ($path) {
            $this->path = DIRECTORY_SEPARATOR.$path.DIRECTORY_SEPARATOR;
        } else {
            $this->path = DIRECTORY_SEPARATOR;
        }
        return $this;
    }

    public function getIndexHeader()
    {
        $header = '';
        foreach ($this->fields as $field) {
            $header .= str_repeat(' ', 12);
            $header .= '<td>'.ucwords(str_replace('_', ' ', $field['name'])).'</td>'.PHP_EOL;
        }
        return $header;
    }

    public function getIndexRow()
    {
        $row = '';
        foreach ($this->fields as $field) {
            $row .= str_repeat(' ', 16).'<td>{{$item->'.$field['name'].'}}</td>'.PHP_EOL;
        }
        return $row;
    }

    public function getViewDir()
    {
        return snake_case($this->name);
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

    public function generate()
    {
        if ($this->layout == 'layouts.master') {
            $this->generateLayout();
        }
        $this->generateIndex();
        $this->generateShow();
        $this->generateForm();
    }

    protected function generateLayout()
    {
        $content = str_replace(
            ['DummyContentSection'],
            [$this->contentSection],
            $this->getStubContent(self::STUB_LAYOUT)
        );

        $this->createFile('master.blade.php', resource_path('views/layouts'), $content, true);
    }

    protected function generateIndex()
    {
        $content = str_replace(
            ['DummyHeader', 'DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getIndexHeader(), $this->getIndexRow(), snake_case($this->name), $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_INDEX)
        );

        $viewDir = $this->getViewDir();
        $this->createFile('index.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function generateShow()
    {
        $content = str_replace(
            ['DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getShowRow(), snake_case($this->name), $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_SHOW)
        );

        $viewDir = $this->getViewDir();
        $this->createFile('show.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function generateForm()
    {
        $content = str_replace(
            ['DummyFieldset', 'DummyLayout', 'DummySection'],
            [$this->getFieldset(), $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_FORM)
        );

        $viewDir = $this->getViewDir();

        $this->createFile('form.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function getFieldset()
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
