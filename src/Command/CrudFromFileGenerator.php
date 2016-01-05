<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class CrudFromFileGenerator extends Command
{
    protected $signature = 'crud:from-file';

    protected $description = 'Generates model, controller and views based on config file';

    public function handle()
    {
        $generator = require resource_path('crud/generator.php');
        foreach ($generator as $name => $config) {
            Artisan::call('crud:generate', $this->buildOptions($name, $config));
        }
    }

    protected function buildOptions($name, array $config)
    {
        $options = ['name' => $name];
        $this->buildFields($options, $config);
        $this->buildController($options, $config);
        $this->buildModel($options, $config);
        $this->buildView($options, $config);
        $this->buildRoute($options, $config);
        return $options;
    }

    protected function buildFields(array &$options, array $config)
    {
        $fields = [];
        $validators = [];
        foreach ($config['fields'] as $field => $meta) {
            $fields[] = $field.':'.$meta['type'];
            if (isset($meta['rules'])) {
                $validators[] = $field.":".$meta['rules'];
            }
        }
        $options['--fields'] = implode(',', $fields);
        if (count($validators) > 0) {
            $options['--validator'] = implode(',', $validators);
        }
    }

    protected function buildController(array &$options, array $config)
    {
        if (isset($config['controller'])) {
            if (isset($config['controller']['path'])) {
                $options['--controller-path'] = $config['controller']['path'];
            }
            if (isset($config['controller']['namespace'])) {
                $options['--controller-namespace'] = $config['controller']['namespace'];
            }
        }
    }

    protected function buildModel(array &$options, array $config)
    {
        if (isset($config['model'])) {
            if (isset($config['model']['path'])) {
                $options['--model-path'] = $config['model']['path'];
            }
            if (isset($config['model']['namespace'])) {
                $options['--model-namespace'] = $config['model']['namespace'];
            }
        }
    }

    protected function buildView(array &$options, array $config)
    {
        if (isset($config['view'])) {
            if (isset($config['view']['path'])) {
                $options['--view-path'] = $config['view']['path'];
            }
            if (isset($config['view']['layout'])) {
                $options['--layout'] = $config['view']['layout'];
            }
        }
    }

    protected function buildRoute(array &$options, array $config)
    {
        if (isset($config['with-route']) && $config['with-route'] === true) {
            $options['--with-route'] = true;
        }
    }
}
