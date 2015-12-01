<?php

namespace Clowdy\Raven\Tests;

use Clowdy\Raven\Log;
use Illuminate\Log\Writer;
use Mockery;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit_Framework_TestCase;

class LogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_extends_illuminate_log_writer()
    {
        $log = Mockery::mock(Log::class);

        $this->assertInstanceOf(Writer::class, $log);
    }

    public function test_register_handler()
    {
        $monolog = Mockery::mock(Logger::class)->makePartial();
        $log = Mockery::mock(Log::class, [$monolog])->makePartial();
        $handler = Mockery::mock(NullHandler::class);

        $monolog->shouldReceive('pushHandler')->once()->with($handler);

        $log->registerHandler('error', function ($level) use ($handler) {
            return $handler;
        });
    }

    public function test_using_context_for_exceptions()
    {
        $monolog = Mockery::mock(Logger::class)->makePartial();
        $log = new Log($monolog);

        $exception = new \Exception('error');

        $monolog->shouldReceive('error')->once()->with('error', ['exception' => $exception]);

        $log->error($exception);
    }
}
