<?php

namespace Clowdy\Raven\Tests;

use PHPUnit_Framework_TestCase;
use Mockery;

class LogTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_extends_illuminate_log_writer()
    {
        $log = Mockery::mock('Clowdy\Raven\Log');

        $this->assertInstanceOf('Illuminate\Log\Writer', $log);
    }

    public function test_register_handler()
    {
        $monolog = Mockery::mock('Monolog\Logger')->makePartial();
        $log = Mockery::mock('Clowdy\Raven\Log', [$monolog])->makePartial();
        $handler = Mockery::mock('Monolog\Handler\NullHandler');

        $monolog->shouldReceive('pushHandler')->once()->with($handler);

        $log->registerHandler('error', function ($level) use ($handler) {
            return $handler;
        });
    }

    public function test_using_context_for_exceptions()
    {
        $monolog = Mockery::mock('Monolog\Logger')->makePartial();
        $log = new \Clowdy\Raven\Log($monolog);

        $exception = new \Exception('error');

        $monolog->shouldReceive('error')->once()->with('error', ['exception' => $exception]);

        $log->error($exception);
    }
}
