<?php

namespace Wilgucki\Crud\Generators;

class View extends Generator
{
    const STUB_LAYOUT = 'master.blade.stub';
    const STUB_PARTIAL_FORM_ELEMENT = 'form-element.blade.stub';
    const STUB_PARTIAL_INPUT = 'input.stub';
    const STUB_PARTIAL_RADIO = 'boolean.stub';
    const STUB_INDEX = 'index.blade.stub';
    const STUB_SHOW = 'show.blade.stub';
    const STUB_FORM = 'form.blade.stub';

    protected $layout = 'layouts.master';
    protected $contentSection = 'content';
    protected $path;
    protected $theme = 'default';

    protected $fieldTypes = [
        'bigInteger' => 'text',
        'boolean' => 'radio',
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

    public function setTheme($theme)
    {
        if ($theme) {
            $this->theme = $theme;
        }
        return $this;
    }

    public function getIndexHeader()
    {
        $header = '';
        foreach ($this->fields as $field) {
            $header .= str_repeat(' ', 12);
            $header .= '<th>'.ucwords(str_replace('_', ' ', $field['name'])).'</th>'.PHP_EOL;
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
            $row .= str_repeat(' ', 16).'<td>{{ $item->'.$field['name'].' }}</td>'.PHP_EOL;
            $row .= str_repeat(' ', 12).'</tr>'.PHP_EOL;
        }
        return $row;
    }

    public function generate()
    {
        if ($this->layout == 'layouts.master') {
            $this->generateLayout();
        }
        $this->generatePartials();
        $this->generateIndex();
        $this->generateShow();
        $this->generateForm();
    }

    protected function generateLayout()
    {
        $content = str_replace(
            ['DummyContentSection'],
            [$this->contentSection],
            $this->getStubContent(self::STUB_LAYOUT, $this->theme)
        );

        $this->createFile('master.blade.php', resource_path('views/layouts'), $content, true);
    }

    protected function generatePartials()
    {
        $this->createFile(
            'form-element.blade.php',
            resource_path('views/partials'),
            $this->getStubContent(self::STUB_PARTIAL_FORM_ELEMENT, $this->theme),
            true
        );
    }

    protected function generateIndex()
    {
        $content = str_replace(
            ['DummyHeader', 'DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getIndexHeader(), $this->getIndexRow(), snake_case($this->name),
                $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_INDEX, $this->theme)
        );

        $viewDir = $this->getViewDir();
        $this->createFile('index.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function generateShow()
    {
        $content = str_replace(
            ['DummyRow', 'DummyModelRoute', 'DummyLayout', 'DummySection'],
            [$this->getShowRow(), snake_case($this->name), $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_SHOW, $this->theme)
        );

        $viewDir = $this->getViewDir();
        $this->createFile('show.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function generateForm()
    {
        $content = str_replace(
            ['DummyFieldset', 'DummyLayout', 'DummySection'],
            [$this->getFieldset(), $this->layout, $this->contentSection],
            $this->getStubContent(self::STUB_FORM, $this->theme)
        );

        $viewDir = $this->getViewDir();

        $this->createFile('form.blade.php', resource_path('views'.$this->path.$viewDir), $content, true);
    }

    protected function getFieldset()
    {
        $row = '';
        foreach ($this->fields as $field) {
            $row .= str_repeat(' ', 8);
            $row .= "@include('partials.form-element',";
            $row .= "['name' => '{$field['name']}', 'type' => '{$field['type']}',";
            $row .= "'field' => {$this->getField($field['name'], $field['type'])}]";
            $row .= ")".PHP_EOL;
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
                $content = $this->getStubContent(self::STUB_PARTIAL_RADIO, $this->theme);
                break;
            default:
                $content = $this->getStubContent(self::STUB_PARTIAL_INPUT, $this->theme);
        }
        $formField = str_replace(
            ['DummyType', 'DummyName'],
            [$this->getFormFieldType($type), $name],
            $content
        );
        return $formField;
    }
}
