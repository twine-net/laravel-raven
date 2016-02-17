<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enable raven logger
    |--------------------------------------------------------------------------
    |
    | Enable raven logger or not
    |
    */
    'enabled' => env('RAVEN_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Raven DSN
    |--------------------------------------------------------------------------
    |
    | Your project's DSN, found under 'API Keys' in your project's settings.
    |
    */
    'dsn' => env('RAVEN_DSN', ''),

    /*
    |--------------------------------------------------------------------------
    | Log Level
    |--------------------------------------------------------------------------
    |
    | Log level at which to log to Sentry. Default `error`.
    |
    | Available: 'debug', 'info', 'notice', 'warning', 'error',
    |            'critical', 'alert', 'emergency'
    |
    */
    'level' => env('RAVEN_LEVEL', 'error'),

    /*
    |--------------------------------------------------------------------------
    | Queue connection
    |--------------------------------------------------------------------------
    |
    | Choose a custom queue connection to use from your config/queue.php
    |
    | Defaults to the default connection and queue from config/queue.php
    |
    */
    'queue' => [
        'connection' => env('RAVEN_QUEUE_CONNECTION', ''),
        'name' => env('RAVEN_QUEUE_NAME', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monolog
    |--------------------------------------------------------------------------
    |
    | Customise the Monolog Raven handler.
    |
    */
    'monolog' => [

        /*
        |--------------------------------------------------------------------------
        | Processors
        |--------------------------------------------------------------------------
        |
        | Set extra data on every log made to Sentry.
        | Monolog has a number of built-in processors which you can find here:
        |
        | https://github.com/Seldaek/monolog/tree/master/src/Monolog/Processor
        |
        | Processors can be an instance of a class, a clousure or the path to a class
        |
        */
        'processors' => [
            // Uncomment the following line to include user data from Auth
            // Clowdy\Raven\Processors\UserDataProcessor::class,

            // Use a built-in Monolog processor
            // Monolog\Processor\GitProcessor::class
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Raven Configuration
    |--------------------------------------------------------------------------
    |
    | Any values below will be passed directly as configuration to the Raven
    | instance. For more information about the possible values check
    | out: https://github.com/getsentry/raven-php
    |
    | Example: "name", "release", "tags", "trace", "timeout", "exclude", "extra", ...
    |
    */
    'options' => [],

];
