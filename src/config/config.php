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
    'enabled' => true,

    /*
	|--------------------------------------------------------------------------
	| Raven DSN
	|--------------------------------------------------------------------------
	|
	| Your project's DSN, found under 'API Keys' in your project's settings.
	|
	*/

    'dsn' => '',

    /*
	|--------------------------------------------------------------------------
	| Log Level
	|--------------------------------------------------------------------------
	|
	| Log level at which to log to Sentry. Default `error`.
	|
	| Available: 'debug', 'info', 'notice', 'warning', 'error',
	| 			 'critical', 'alert', 'emergency'
	|
	*/

    'level' => 'error',

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
        'connection' => '',
        'queue' => ''
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
        | https://github.com/Seldaek/monolog/blob/master/README.mdown#processors
        |
        */

        'processors' => [
            // 'Monolog\Processor\GitProcessor'
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
	| Example: "name", "tags", "trace", "timeout", "exclude", "extra", ...
	|
	*/

    'options' => []

];
