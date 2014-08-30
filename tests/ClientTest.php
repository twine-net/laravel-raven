<?php namespace Clowdy\Raven\Tests;

use PHPUnit_Framework_TestCase;
use Mockery as m;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_extends_raven_client()
    {
        $client = m::mock('Clowdy\Raven\Client');

        $this->assertInstanceOf('Raven_Client', $client);
    }

    public function test_queue_data()
    {
        $queue = m::mock('Illuminate\Queue\QueueManager')->makePartial();
        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], null);

        $client = m::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();
        
        $client->send([]);
    }

    public function test_setting_custom_queue()
    {
        $queue = m::mock('Illuminate\Queue\QueueManager')->makePartial();
        $queue->shouldReceive('push')->once()->with('Clowdy\Raven\Job', [], 'errors');

        $client = m::mock('Clowdy\Raven\Client', [[], $queue])->makePartial();
        $client->setCustomQueue('errors');
        
        $client->send([]);
    }

}
