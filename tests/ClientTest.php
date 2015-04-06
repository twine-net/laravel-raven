<?php

namespace Clowdy\Raven\Tests;

use PHPUnit_Framework_TestCase;
use Mockery;

class ClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    protected $queue;

    protected $session;

    public function setUp()
    {
        parent::setUp();
 
        $this->client = Mockery::mock('Clowdy\Raven\Client')->makePartial();
        $this->queue = Mockery::mock('Illuminate\Queue\QueueManager')->makePartial();
        $this->session = Mockery::mock('Illuminate\Session\SessionManager')->makePartial();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_extends_raven_client()
    {
        $this->assertInstanceOf('Raven_Client', $this->client);
    }

    public function test_queue_data()
    {
        $this->client->setQueue($this->queue);

        $this->queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null);

        $this->client->send([]);
    }

    public function test_setting_custom_queue()
    {
        $this->client->setQueue($this->queue);
        $this->client->setCustomQueue('errors');

        $this->queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], 'errors');

        $this->client->send([]);
    }

    public function test_should_send_error_directly_if_queue_not_set()
    {
        $this->client->shouldReceive('sendError')->once()->with([]);

        $this->client->send([]);
    }

    public function test_should_send_error_directly_if_queue_fails()
    {
        $this->client->setQueue($this->queue);

        $this->queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null)->andThrow(new \Exception());
        $this->client->shouldReceive('sendError')->once()->with([]);

        $this->client->send([]);
    }

    public function test_sending_error_no_server()
    {
        $this->client->setQueue($this->queue);

        $this->assertNull($this->client->sendError([]));
    }

    public function test_getting_user_data_from_context()
    {
        $this->client->setQueue($this->queue);
        $this->client->setSession($this->session);

        $this->session->shouldReceive('all')->once()->andReturn([]);
        $this->session->shouldReceive('getId')->times(0);

        $this->client->set_user_data(1, 'user@example.com', ['data' => ['type' => 'vip']]);

        $this->assertEquals($this->client->get_user_data(), [
            'sentry.interfaces.User' => [
                'id' => 1,
                'email' => 'user@example.com',
                'data' => ['type' => 'vip'],
            ]
        ]);
    }

    public function test_getting_user_data_with_sessions()
    {
        $this->client->setQueue($this->queue);
        $this->client->setSession($this->session);

        $this->session->shouldReceive('all')->once()->andReturn([]);
        $this->session->shouldReceive('getId')->once()->andReturn(1);

        $this->assertEquals($this->client->get_user_data(), [
            'sentry.interfaces.User' => [
                'data' => [],
                'id' => 1,
            ]
        ]);
    }

    public function test_getting_user_data_without_sessions()
    {
        $this->client->setQueue($this->queue);

        $this->assertEquals($this->client->get_user_data(), ['sentry.interfaces.User' => []]);
    }
}
