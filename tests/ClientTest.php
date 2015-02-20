<?php

namespace Clowdy\Raven\Tests;

use PHPUnit_Framework_TestCase;
use Mockery;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function getQueueManager()
    {
        return Mockery::mock('Illuminate\Queue\QueueManager')->makePartial();
    }

    public function getSessionManager()
    {
        return Mockery::mock('Illuminate\Session\SessionManager')->makePartial();
    }

    public function test_extends_raven_client()
    {
        $client = Mockery::mock('Clowdy\Raven\Client');

        $this->assertInstanceOf('Raven_Client', $client);
    }

    public function test_queue_data()
    {
        $queue = $this->getQueueManager();

        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();

        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null);

        $client->send([]);
    }

    public function test_setting_custom_queue()
    {
        $queue = $this->getQueueManager();

        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();
        $client->setCustomQueue('errors');

        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], 'errors');

        $client->send([]);
    }

    public function test_should_send_error_directly_if_queue_fails()
    {
        $queue = $this->getQueueManager();

        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();

        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null)->andThrow(new \Exception());
        $client->shouldReceive('sendError')->once()->with([]);

        $client->send([]);
    }

    public function test_sending_error_no_server()
    {
        $queue = $this->getQueueManager();
        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();

        $this->assertNull($client->sendError([]));
    }

    public function test_getting_user_data_with_sessions()
    {
        $queue = $this->getQueueManager();
        $session = $this->getSessionManager();
        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue, $session])->makePartial();

        $session->shouldReceive('all')->once()->andReturn([]);
        $session->shouldReceive('getId')->once()->andReturn(1);

        $this->assertEquals($client->get_user_data(), [
            'sentry.interfaces.User' => [
                'data' => [],
                'id' => 1,
            ]
        ]);
    }

    public function test_getting_user_data_without_sessions()
    {
        $queue = $this->getQueueManager();
        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();

        $this->assertEquals($client->get_user_data(), ['sentry.interfaces.User' => []]);
    }
}
