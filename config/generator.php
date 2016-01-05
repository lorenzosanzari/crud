<?php

return [
    'Foo' => [
        'fields' => [
            'name' => [
                'type' => 'string',
                'rules' => 'required|max:150'
            ],
            'email' => [
                'type' => 'string',
                'rules' => 'required|email|unique:foos'
            ]
        ],
        'controller' => [
            'path' => 'Admin',
            'namespace' => 'Admin'
        ],
        'model' => [
            'path' => 'Models',
            'namespace' => 'App\Models'
        ],
        'view' => [
            'layout' => 'layouts.admin',
            'path' => 'admin'
        ],
        'with-route' => true
    ]
];
