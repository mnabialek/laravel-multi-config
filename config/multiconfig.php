<?php

return [
    /**
     * Available modes:
     * - env - you want to set environment from env file
     * - host - you want to set environment based on your HTTP host
     */
    'env_mode' => 'env',

    /**
     * Available modes:
     * - config - if you want to use config files in Laravel 4 way
     * - null - if you want to use default Laravel 5 config style
     */
    'config_mode' => 'config',

    /**
     * Bootstrappers that will be overridden by custom ones
     */
    'bootstrappers' => [
        'Illuminate\Foundation\Bootstrap\DetectEnvironment' =>
            'Mnabialek\LaravelMultiConfig\DetectEnvironment',
        'Illuminate\Foundation\Bootstrap\LoadConfiguration' =>
            'Mnabialek\LaravelMultiConfig\LoadConfiguration',
    ],

    /**
     * Custom folders that will be used also to set config. Configs in this paths
     * will be merged in given order with default laravel config and finally
     * environment configuration will be merged with them
     */
    'config_extra_folders' => [
        // you might overload in this one default Laravel configuration if
        // you don't want to make changes in default Laravel configuration files
        config_path('custom'),
        // you might overload in this one configuration for specific server.
        // Assume you have 5 environments on server x and you want to set
        // config for each of them to y. In this directory you can create
        // this configuration file and you will set this for all environments
        // on this server
        config_path('server'),
    ],

    /**
     * Custom paths
     */
    'paths' => [
        // path for environments .env files
        'env_folder' => base_path(),
        // path for environments config
        'env_config_folder' => config_path(),
    ],

    /**
     * Default environment when there is no host set. It should be set to
     * something reasonable to make sure it won't brake this environment.
     * The best will be testing environment. You might change it into something
     * else if you really know what are you doing
     */
    'no_host_default_environment' => 'testing',
];
