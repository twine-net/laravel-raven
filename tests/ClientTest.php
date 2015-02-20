<?php namespace Clowdy\Raven\Tests;

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

    public function getEnv()
    {
        return 'testing';
    }

    public function test_extends_raven_client()
    {
        $client = Mockery::mock('Clowdy\Raven\Client');

        $this->assertInstanceOf('Raven_Client', $client);
    }

    public function test_queue_data()
    {
        $queue = $this->getQueueManager();
        $session = $this->getSessionManager();
        $env = $this->getEnv();

        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue, $session, $env])->makePartial();
        
        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null);

        $client->send([]);
    }

    public function test_setting_custom_queue()
    {
        $queue = $this->getQueueManager();
        $session = $this->getSessionManager();
        $env = $this->getEnv();

        $client = Mockery::mock('Clowdy\Raven\Client', [[], $queue, $session, $env])->makePartial();
        $client->setCustomQueue('errors');
        
        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], 'errors');

        $client->send([]);
    }

}
