<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Generator config
    |--------------------------------------------------------------------------
    |
    | All config settings for the Artisan command for creating new Repositories.
    |
    */
    'generator' => [
        /*
         * The path where new entity interfaces are created.
         */
        'entity_interface_path' => app()->basePath('app/{namespace}/Contracts/{name}.php'),
        /*
         * The path where new entities are created.
         */
        'entity_path' => app()->basePath('app/{namespace}/Entities/{name}.php'),
        /*
         * The path where new repository interfaces are created.
         */
        'interface_path' => app()->basePath('app/{namespace}/Repositories/{name}.php'),
        /*
         * The path where new repository implementations are created.
         */
        'repository_path' => app()->basePath('app/{namespace}/Repositories/{implementation}/{name}.php'),
        /*
         * The path where new migrations are created.
         */
        'migration_path' => app()->basePath('migrations'),

        /*
         * The namespace used for Entity interfaces. {namespace} is replaced by the --namespace option.
         */
        'entity_interface_namespace' => 'App\\{namespace}\\Contracts\\',
        /*
         * The namespace used for Entities. {namespace} is replaced by the --namespace option.
         */
        'entity_namespace' => 'App\\{namespace}\\Entities\\',
        /*
         * The namespace used for the interface. {namespace} is replaced by the --namespace option.
         */
        'interface_namespace' => 'App\\{namespace}\\Repositories\\',
        /*
         * The namespace used for Repositories. {namespace} is replaced by the --namespace option.
         */
        'repository_namespace' => 'App\\{namespace}\\Repositories\\{implementation}\\',

        /*
         * The prefix for Entity interfces
         */
        'entity_interface_prefix' => '',
        /*
         * The suffix for Entity interfaces
         */
        'entity_interface_suffix' => 'Contract',
        /*
         * The prefix for Entities
         */
        'entity_prefix' => '',
        /*
         * The suffix for Entities
         */
        'entity_suffix' => '',
        /*
         * The prefix for Repository Interfaces
         */
        'interface_prefix' => '',
        /*
         * The suffix for Repository Interfaces
         */
        'interface_suffix' => 'RepositoryContract',
        /*
         * The prefix for Repository Interfaces
         */
        'repository_prefix' => '',
        /*
         * The suffix for Repository Interfaces
         */
        'repository_suffix' => 'Repository',

        /*
         * The path that the filesystem needs to use as root.
         */
        'filesystem' => [
            'root' => app()->resourcePath(),
        ],

        /*
         * The list of available implementations.
         *
         * This should be an array of 'name' => 'full\namespace\to\abstract\base\class'.
         */
        'available_implementations' => [
            'eloquent' => \Davesweb\Repositories\Implementations\Eloquent\EloquentRepository::class,
        ],

        /*
         * The stub files to use
         */
        'stubs' => [
            'entity'           => app()->resourcePath('stubs/EntityClass.stub'),
            'entity_interface' => app()->resourcePath('stubs/EntityInterface.stub'),
            'interface'        => app()->resourcePath('stubs/RepositoryInterface.stub'),
            'repository'       => app()->resourcePath('stubs/RepositoryClass.stub'),
        ],
    ],
];
