<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class CrudGenerator extends Command
{
    protected $signature = 'crud:generate
                            {name : Name of the model, controller and }
                            {--fields= : Fields for migration, model and views}
                            {--validator= : Validator rules}
                            {--with-route : Adds route to routes.php}
                            {--controller-path= : Controller path relative to Controllers dir}
                            {--view-path= : Relative to views directory path where view files will be created}
                            {--layout= : Name of the layout to extend}
                            {--theme= : View theme}
                            {--model-path= : Relative to app directory}
                            {--controller-namespace= : Use custom namespace in your controller}
                            {--model-namespace= : Custom model namespace}';

    protected $description = 'Generates model, migration, controller and views from given values';

    public function handle()
    {
        $fillable = [];
        $fields = explode(',', $this->option('fields'));
        foreach ($fields as $field) {
            $fillable[] = trim(explode(':', $field)[0]);
        }

        $this->info('Generating model...');
        Artisan::call(
            'crud:model',
            [
                'name' => $this->argument('name'),
                '--fillable' => implode(',', $fillable),
                '--namespace' => $this->option('model-namespace') ? $this->option('model-namespace') : null,
                '--path' => $this->option('model-path') ? $this->option('model-path') : null
            ]
        );
        $this->info('Done');

        $this->info('Generating migration...');
        Artisan::call(
            'crud:migration',
            [
                'name' => $this->argument('name'),
                '--fields' => $this->option('fields')
            ]
        );
        $this->info('Done');

        $this->info('Generating controller...');
        Artisan::call(
            'crud:controller',
            [
                'name' => $this->argument('name'),
                '--with-route' => $this->option('with-route'),
                '--path' => $this->option('controller-path') ? $this->option('controller-path') : null,
                '--view-path' => $this->option('view-path') ? $this->option('view-path') : null,
                '--namespace' => $this->option('controller-namespace') ? $this->option('controller-namespace') : null,
                '--model' => $this->option('model-namespace')
                    ? $this->option('model-namespace').'\\'.$this->argument('name')
                    : null,
                '--validator' => $this->option('validator') ? $this->option('validator') : null
            ]
        );
        $this->info('Done...');

        $this->info('Generating views...');
        Artisan::call(
            'crud:view',
            [
                'name' => $this->argument('name'),
                '--fields' => $this->option('fields'),
                '--path' => $this->option('view-path') ? $this->option('view-path') : null,
                '--layout' => $this->option('layout') ? $this->option('layout') : null,
                '--theme' => $this->option('theme') ? $this->option('theme') : null
            ]
        );
        $this->info('Done');
        $this->info('All done! Make something beautiful!');
    }
}
