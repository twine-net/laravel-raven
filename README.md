Laravel Raven
=============

> Sentry (Raven) error monitoring for Laravel 5 with send in background using queues

[![Build Status](http://img.shields.io/travis/clowdy/laravel-raven/master.svg?style=flat-square)](https://travis-ci.org/clowdy/laravel-raven)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/clowdy/laravel-raven/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/clowdy/laravel-raven/)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/clowdy/laravel-raven/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/clowdy/laravel-raven/code-structure/master)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](http://www.opensource.org/licenses/MIT)
[![Latest Version](http://img.shields.io/packagist/v/clowdy/laravel-raven.svg?style=flat-square)](https://packagist.org/packages/clowdy/laravel-raven)
[![Total Downloads](https://img.shields.io/packagist/dt/clowdy/laravel-raven.svg?style=flat-square)](https://packagist.org/packages/clowdy/laravel-raven)

Sentry (Raven) error monitoring for Laravel 5 with send in background using queues. This will add a listener to Laravel's existing log system. It makes use to Laravel's queues to push messages into the background without slowing down the application.

![rollbar](https://www.getsentry.com/_static/getsentry/images/hero.png)

## Installation

Add the package to your `composer.json` and run `composer update`.

```js
{
    "require": {
        "clowdy/laravel-raven": "2.*"
    }
}
```

Add the service provider in `config/app.php`:

```php
'Clowdy\Raven\RavenServiceProvider',
```

Register the Raven alias:

```php
'Raven' => 'Clowdy\Raven\Facades\Raven',
```

You also need to modify `app\Exceptions\Hander.php` to extend `Clowdy\Raven\Handler` instead of `Illuminate\Foundation\Exceptions\Handler` as below:

```php
<?php namespace app\Exceptions;

use Exception;
// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Clowdy\Raven\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
```

### Looking for a Laravel 4 compatible version?

Checkout the [1.0 branch](https://github.com/clowdy/laravel-raven/tree/1.0)

## Configuration

Publish the included configuration file:

```bash
$ php artisan vendor:publish --provider="clowdy\laravel-raven\RavenServiceProvider" --tag="config"
```

Change the Sentry DSN by using the `RAVEN_DSN` env variable or changing the config file:

```php
RAVEN_DSN=your-raven-dsn
```

This library uses the queue system, make sure your `config/queue.php` file is configured correctly. You can also specify the connection and the queue to use in the raven config. Connection must exist in `config/queue.php` and a custom queue can be defined. These can be set using the `RAVEN_QUEUE_CONNECTION` for connection and `RAVEN_QUEUE_QUEUE` for the custom queue.

```php
RAVEN_QUEUE_CONNECTION=beanstalkd
RAVEN_QUEUE_QUEUE=error
```

## Usage

To monitor exceptions, simply use the `Log` facade:

```php
Log::error($exception);
```

You can change the logs used by changing the log level in the config by modifying the env var.

```php	
RAVEN_LEVEL=error
```

### Context information

You can pass additional information as context like this:

```php
Log::error('Oops, Something went wrong', [
    'user' => ['name' => $user->name, 'email' => $user->email]
]);
```

## Credits

This package was inspired [rcrowe/Raven](https://github.com/rcrowe/Raven).
