<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class CrudGenerator extends Command
{
    protected $signature = 'crud:generate
                            {name : Name of the model, controller and }
                            {--fields= : Fields for migration, model and views}';

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
                '--fillable' => implode(',', $fillable)
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
            ['name' => $this->argument('name')]
        );
        $this->info('Done...');

        $this->info('Generating views...');
        Artisan::call(
            'crud:view',
            [
                'name' => $this->argument('name'),
                '--fields' => $this->option('fields')
            ]
        );
        $this->info('Done');
        $this->info('All done! Make something beautiful!');
    }
}
