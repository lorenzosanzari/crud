#Laravel Crud Generator

This is the first release of Crud package and it needs a lot of testing and even more improvements.
For now it provides basic (but complete) functionality of crud generator. Generated views are plain HTML views without signle line of
CSS or JavaScript code.
Laravel Crud Generator is not production ready.

##Instalation

To install Laravel Crud Generator you need to run composer command:

<code>composer require wilgucki/crud</code>

Next add service provider to app.php config file.

    'providers' => [
        //... 
        Wilgucki\Csv\CrudServiceProvider::class,
    ]

Last step is to publish package files with command

<code>php artisan vendor:publish --provider=Wilgucki\\Crud\\CrudServiceProvider</code>

##Usage

Laravel Crud Generator offers few artisan commands that will speed up your work.

###Creating Crud From Database Table

<code>php artisan crud:from-database table_name --controller-path=Admin --controller-namespace=Admin --view-path=admin --model-namespace=App\Models --with-route</code>

This command allows you to generate crud (controller, model and views) based on specified table.

Available options:

- with-route (optional) - Adds route to routes.php
- controller-path (optional) - Controller path relative to Http/Controllers dir
- view-path (optional) - Relative to views directory path where view files will be created
- controller-namespace (optional) - Use custom namespace in your controller
- model-namespace (optional) - Custom model namespace

###Creating Crud

<code>php artisan crud:generate SomeName --fields=name:string,description:text,is_public:boolean,added_at:dateTime --controller-path=Admin --controller-namespace=Admin --view-path=admin --model-namespace=App\Models --with-route</code>

Crud:generate command will create model, controller, migration and views files based on given name and fileds.
After you run this command you will need to run migration command to create database table.

<code>php artisan migrate</code>

Available options:

- fields (required) - Fields used by migration, model and views
- with-route (optional) - Adds route to routes.php
- controller-path (optional) - Controller path relative to Http/Controllers dir
- view-path (optional) - Relative to views directory path where view files will be created
- controller-namespace (optional) - Use custom namespace in your controller
- model-namespace (optional) - Custom model namespace

###Creating Model

<code>php artisan crud:model SomeModel --table=table_name --fillable=name,description --namespace=App\\SomeNamespace</code>

You can generate model using above command. You need to specify model name. Remaining parameters are optional. You can use them to:

- table (optional) - change database table name. Default name is generated based on model name
- fillable (optional) - list of filds added to <code>$fillable</code> array
- namespace (optional) - use this option to change models' namespace. Default value - App

###Creating Migration

<code>php artisan crud:migration SomeName --fields=name:string,description:text,added_at:dateTime</code>

Creates database migration named CreateSomeNamesTable. Required option --fileds accepts key:value pairs, where key is field name and value is
column type. You can find these types in [official Laravel documentation](https://laravel.com/docs/5.2/migrations#creating-columns).

###Creating Controller

<code>php artisan crud:controller SomeName --model=ModelName --namespace=App\\SomeNamespace --path=Admin --with-route</code>

This command generates resource controller. You only need to specify controller name without Controller suffix.
Optional parameters you can use:

- model (optional) - name of the model to use for database access. Default value is the same as controller name
- namespace (optional) - custom namespace. Default value - App\Http\Controllers
- path (optional) - place your controller in directory relative to Http/Controllers dir
- with-route (optional) - using this option will create resource route in routes.php file

###Creating Views

<code>php artisan crud:view SomeName --fields=name:string,description:text,added_at:dateTime --layout=layouts.blog.master --content-section=page_content --path=admin</code>

Last but not least - generating views. To generate view you need to pass view name as well as list of fields.
You can find field types in [official Laravel documentation](https://laravel.com/docs/5.2/migrations#creating-columns).
Other options are:

- layout  (optional)- extend custom layout. Default value - layouts.master (generator will create this file)
- content-section  (optional)- name of the blade section where view will be generated. Default value - content
- path  (optional)- use this option if you want to place your views deeper in directory structure

##TODO (custom order)

- add validation in the form of [Form Request Validation](https://laravel.com/docs/5.2/validation#form-request-validation)
- create generator that builds crud based on config file
- rewrite generators and stubs to use more user friendly variables (instead of $item)
- test this package against Laravel 5.1
- improve README, check for typos and other errors
- add, as option, Bootstrap
- add description of stubs and how to customize them
- allow to define actions generated by controller generator
