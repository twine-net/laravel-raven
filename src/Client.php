<?php

namespace Clowdy\Raven;

use Exception;
use Illuminate\Queue\QueueManager;
use Raven_Client;

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
     * @param array                                   $config,
     * @param \Illuminate\Queue\QueueManager          $queue
     * @param \Illuminate\Session\SessionManager|null $session
     * @param string|null                             $env
     */
    public function __construct(array $config, QueueManager $queue, $session = null, $env = null)
    {
        $dsn = array_get($config, 'dsn', '');

        $options = ['tags' => ($env) ? ['environment' => $env] : []];
        $options = array_replace_recursive($options, array_get($config, 'options', []));

        parent::__construct($dsn, $options);

        $this->queue = $queue;
        $this->session = $session;
    }

    /**
     * Setter for a custom queue
     *
     * @return \Clowdy\Raven\Client
     */
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

        if (!is_null($this->session)) {
            $session = $this->session->all();

            // Add Laravel session data
            if (isset($user['data'])) {
                $user['data'] = array_merge($session, $user['data']);
            } else {
                $user['data'] = $session;
            }

            // Add session id
            if (!isset($user['id'])) {
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
    public function send($data)
    {
        // put the job into the queue
        // Sync connection will sent directly
        // if failed to add job to queue send it now
        try {
            $this->queue->push('Clowdy\Raven\Job', $data, $this->customQueue);
        } catch (Exception $e) {
            $this->sendError($data);
        }
    }

    /**
     * Send the error to sentry without queue
     *
     * @return void
     */
    public function sendError($data)
    {
        parent::send($data);
    }
}
