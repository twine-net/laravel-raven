<?php

namespace Clowdy\Raven\Tests;

use Clowdy\Raven\Client;
use Clowdy\Raven\Job;
use Illuminate\Queue\QueueManager;
use Mockery;
use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function test_extends_raven_client()
    {
        $this->assertInstanceOf('Raven_Client', new Client([]));
    }

    public function test_queue_data()
    {
        $queue = Mockery::mock(QueueManager::class);
        $client = new Client([], $queue);

        $queue->shouldReceive('connection')->once()->with(null)->andReturn($queue);
        $queue->shouldReceive('push')->once()->with(Job::class, [], null);

        $client->send([]);
    }

    public function test_setting_custom_queue()
    {
        $queue = Mockery::mock(QueueManager::class);
        $client = new Client(['queue' => ['name' => 'errors']], $queue);

        $queue->shouldReceive('connection')->once()->with(null)->andReturn($queue);
        $queue->shouldReceive('push')->once()->with(Job::class, [], 'errors');

        $client->send([]);
    }

    public function test_should_send_error_directly_if_queue_not_set()
    {
        $client = Mockery::mock(Client::class)->makePartial();

        $client->shouldReceive('sendError')->once()->with([]);

        $client->send([]);
    }

    public function test_sending_error_no_server()
    {
        $client = new Client([]);

        $this->assertNull($client->sendError([]));
    }
}
