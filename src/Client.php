<?php

namespace Clowdy\Raven;

use Clowdy\Raven\Job;
use Exception;
use Illuminate\Queue\QueueManager;
use Raven_Client;

class Client extends Raven_Client
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Illuminate\Queue\QueueManager|null
     */
    protected $queue;

    /**
     * @param array $config,
     * @param \Illuminate\Queue\QueueManager $queue
     * @param string|null $env
     */
    public function __construct(array $config, QueueManager $queue = null, $env = null)
    {
        $this->config = $config;
        $this->queue = $queue;

        // merge env into options if set
        $options = array_replace_recursive(
            [
                'tags' => [
                    'environment' => $env,
                    'logger' => 'laravel-raven',
                ],
            ],
            array_get($config, 'options', [])
        );

        parent::__construct(array_get($config, 'dsn', ''), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        // send error now if queue not set
        if (is_null($this->queue)) {
            return $this->sendError($data);
        }

        // put the job into the queue
        // Sync connection will sent directly
        // if failed to add job to queue send it now
        try {
            $this->queue
                ->connection(array_get($this->config, 'queue.connection'))
                ->push(
                    Job::class,
                    $data,
                    array_get($this->config, 'queue.name')
                );
        } catch (Exception $e) {
            return $this->sendError($data);
        }

        return;
    }

    /**
     * Send the error to sentry without queue.
     *
     * @return void
     */
    public function sendError($data)
    {
        return parent::send($data);
    }
}
