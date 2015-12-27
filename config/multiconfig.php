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
        // if you need you can add here also extra folders
    ],

    /**
     * Custom paths
     */
    'paths' => [
        // path for extra environments .env files
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

    /**
     * Extra settings for loading custom env file for environment
     */
    'env_settings' => [
        /**
         * Load extra .env file for environment
         */
        'load_env_file' => true,

        /**
         * Here you might specify custom env files for environments example
         * 'domain1.com' => '.domain5.com.env' - by default if it's not
         * specified here '.domain1.com.env' will be used
         */
        'environment_files' => [],

        /**
         * If no env file exists for current environment (and load_env_file
         * is set to true) - what should be done - if it's null application
         * will stop with info that env file is missing, but you can also
         * put here default env file you would like to use in this case
         */
        'fallback_environment' => null,

        /**
         * Whether env mismatch should be verified. By default (when it's set
         * to true) in case for current environment custom env file is loaded
         * APP_ENV should be set to the same as current environment name. If
         * it's mot, application is stopped with ENV mismatch info. However in
         * some cases you might want to ignore this (change it only if you
         * really know what you are doing)
         */
        'verify_env_mismatch' => true,

        /**
         * If we set verify_env_mismatch to false, it might be useful to hold
         * calculated environment name somewhere. The whole application would
         * use APP_ENV from env file but what if somewhere in app we would like
         * to verify this and take some actions based on that? Here we can set
         * env name where we store calculated environment name. If set to
         * false/null it won't be set
         */
        'real_app_env' => 'REAL_APP_ENV',
    ],
];
