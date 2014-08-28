Laravel Raven
=============

Sentry (Raven) error monitoring for Laravel with send in background. This will add a listener to Laravel's existing log system. It makes use to Laravel's queues to push messages into the background without slowing down the application.

![rollbar](https://www.getsentry.com/_static/getsentry/images/hero.png)

Installation
------------

Add the package to your `composer.json` and run `composer update`.

```js
{
    "require": {
        "clowdy/laravel-raven": "*"
    }
}
```

Add the service provider in `app/config/app.php`:

```php
'Clowdy\Raven\RavenServiceProvider',
```

Register the Raven alias:

```php
'Raven' => 'Clowdy\Raven\Facades\Raven',
```

Configuration
-------------

Publish the included configuration file:

```bash
$ php artisan config:publish clowdy/laravel-raven
```

And change your Sentry DSN:

```php
'dsn' => 'your-raven-dsn',
```

This library uses the queue system, make sure your `config/queue.php` file is configured correctly. You can also specify the connection and the queue to use in the raven config. Connection must exist in `config/queue.php` and a custom queue can be defined.

```php
'queue' => [
	'connection' => 'beanstalkd',
	'queue'      => 'errors'
];
```

Usage
-----

To monitor exceptions, simply use the `Log` facade:

```php
App::error(function(Exception $exception, $code)
{
    Log::error($exception);
});
```

You can change the logs used by changing the log level in the config

```php	
'level' => 'error',
```

### Context informaton

You can pass additional information as context like this:

```php
Log::error('Oops, Something went wrong', [
    'user' => ['name' => $user->name, 'email' => $user->email]
]);
```
