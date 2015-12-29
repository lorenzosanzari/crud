<?php

namespace Wilgucki\Crud\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class CrudFromDbGenerator extends Command
{
    protected $signature = 'crud:from-database
                            {table : Database table used by crud generator}';

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
                '--fillable' => implode(',', $fillable)
            ]
        );
        $this->info('Done');

        $this->info('Generating controller...');
        Artisan::call(
            'crud:controller',
            ['name' => $name]
        );
        $this->info('Done...');

        $this->info('Generating views...');
        Artisan::call(
            'crud:view',
            [
                'name' => $name,
                '--fields' => implode(',', $fields)
            ]
        );
        $this->info('Done');
        $this->info('All done! Make something beautiful!');
    }
}
