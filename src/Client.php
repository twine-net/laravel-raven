<?php

namespace Clowdy\Raven;

use Raven_Client;
use Illuminate\Queue\QueueManager;

class Client extends Raven_Client
{
    /**
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * @var \Illuminate\Session\SessionManager|null
     */
    protected $session;

    /**
     * @var string
     */
    protected $customQueue;

    /**
     * @var string|null
     */
    protected $env;

    /**
     * @param array                                   $config,
     * @param \Illuminate\Queue\QueueManager          $queue
     * @param \Illuminate\Session\SessionManager|null $session
     * @param string|null                             $env
     */
    public function __construct(array $config, QueueManager $queue, $session = null, $env = null)
    {
        $dsn = array_get($config, 'dsn', '');
        $options = array_get($config, 'options', []);

        parent::__construct($dsn, $options);

        $this->queue = $queue;
        $this->session = $session;
        $this->env = $env;
    }

    public function setCustomQueue($queue)
    {
        $this->customQueue = $queue;
    }

    /**
     * {@inheritdoc}
     */
    protected function get_user_data()
    {
        $user = $this->context->user ?: [];

        if (!is_null($session)) {
            $session = $this->session->all();

            // Add Laravel session data
            if (isset($user['data'])) {
                $user['data'] = array_merge($session, $user['data']);
            } else {
                $user['data'] = $session;
            }

            // Add session id
            if (! isset($user['id'])) {
                $user['id'] = $this->session->getId();
            }
        }

        return [
            'sentry.interfaces.User' => $user,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get_default_data()
    {
        // Add additional tags
        if (!is_null($this->env)) {
            $this->tags['environment'] = $this->env;
        }

        return parent::get_default_data();
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        // put the job into the queue
        // Sync connection will sent directly
        // if failed to add job to queue send it now
        try {
            $this->queue->push('Clowdy\Raven\Job', $data, $this->customQueue);
        } catch (\Exception $e) {
            $this->sendError($data);
        }
    }

    public function sendError($data)
    {
        return parent::send($data);
    }
}
