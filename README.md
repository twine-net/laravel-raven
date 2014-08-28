Laravel Raven
=============

Sentry (Raven) error monitoring for Laravel with send in background. This will add a listener to Laravel's existing log system. It makes use to Laravel's queues to push messages into the background without slowing down the application.

![rollbar](https://www.getsentry.com/_static/getsentry/images/hero.png)

Installation
------------

Add the package to your `composer.json` and run `composer update`.

    {
        "require": {
            "clowdy/laravel-raven": "*"
        }
    }

Add the service provider in `app/config/app.php`:

    'Clowdy\Raven\RavenServiceProvider',

Register the Raven alias:

    'Raven' => 'Clowdy\Raven\Facades\Raven',

Configuration
-------------

Publish the included configuration file:

    php artisan config:publish clowdy/laravel-raven

And change your Sentry DSN:

    'dsn' => 'your-raven-dsn',

This library uses the queue system, make sure your `config/queue.php` file is configured correctly. You can specify the connection and the queue to use in the raven config. Connection must exist in `config/queue.php` and a custom queue can also be set.
	
	'queue' => [
		'connection' => 'beanstalkd',
		queue => 'errors'
	]

Usage
-----

To monitor exceptions, simply use the `Log` facade:

    App::error(function(Exception $exception, $code)
    {
        Log::error($exception);
    });

You can change the logging levels by changing the level in the config
	
	'level' => 'error',

### Context informaton

You can pass additional information as context like this:

    Log::error('Oops, Something went wrong', [
        'user' => ['name' => $user->name, 'email' => $user->email]
    ]);