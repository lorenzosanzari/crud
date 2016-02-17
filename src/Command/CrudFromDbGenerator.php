<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class CrudFromDbGenerator extends Command
{
    protected $signature = 'crud:from-database
                            {table : Database table used by crud generator}
                            {--validator= : Validator rules}
                            {--with-route : Adds route to routes.php}
                            {--controller-path= : Controller path relative to Controllers dir}
                            {--view-path= : Relative to views directory path where view files will be created}
                            {--layout= : Name of the layout to extend}
                            {--theme= : View theme}
                            {--model-path= : Relative to app directory}
                            {--controller-namespace= : Use custom namespace in your controller}
                            {--model-namespace= : Custom model namespace}';

    protected $description = 'Generates model, controller and views based on database table';

    public function handle()
    {
        $name = ucfirst(camel_case(str_singular($this->argument('table'))));
        $sm = \DB::getDoctrineSchemaManager();
        $dbColumns = $sm->listTableColumns($this->argument('table'));
        $fillable = [];
        $fields = [];
        foreach ($dbColumns as $field => $meta) {
            $fillable[] = $field;
            $fields[] = $field.':'. camel_case((string)$meta->getType());
        }

        $this->info('Generating model...');
        Artisan::call(
            'crud:model',
            [
                'name' => $name,
                '--fillable' => implode(',', $fillable),
                '--namespace' => $this->option('model-namespace') ? $this->option('model-namespace') : null,
                '--path' => $this->option('model-path') ? $this->option('model-path') : null
            ]
        );
        $this->info('Done');

        $this->info('Generating controller...');
        Artisan::call(
            'crud:controller',
            [
                'name' => $name,
                '--with-route' => $this->option('with-route'),
                '--path' => $this->option('controller-path') ? $this->option('controller-path') : null,
                '--view-path' => $this->option('view-path') ? $this->option('view-path') : null,
                '--namespace' => $this->option('controller-namespace') ? $this->option('controller-namespace') : null,
                '--model' => $this->option('model-namespace')
                    ? $this->option('model-namespace').'\\'.$name
                    : null,
                '--validator' => $this->option('validator') ? $this->option('validator') : null
            ]
        );
        $this->info('Done...');

        $this->info('Generating views...');
        Artisan::call(
            'crud:view',
            [
                'name' => $name,
                '--fields' => implode(',', $fields),
                '--path' => $this->option('view-path') ? $this->option('view-path') : null,
                '--layout' => $this->option('layout') ? $this->option('layout') : null,
                '--theme' => $this->option('theme') ? $this->option('theme') : null
            ]
        );
        $this->info('Done');
        $this->info('All done! Make something beautiful!');
    }
}
