<?php

namespace Clowdy\Raven;

use Exception;
use Raven_Client;

class Client extends Raven_Client
{
    /**
     * @var \Illuminate\Queue\QueueManager|null
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
     * @param \Illuminate\Queue\QueueManager|null     $queue
     * @param \Illuminate\Session\SessionManager|null $session
     * @param string|null                             $env
     */
    public function __construct(array $config, $queue = null, $session = null, $env = null)
    {
        $dsn = array_get($config, 'dsn', '');

        $options = ['tags' => ($env) ? ['environment' => $env] : []];
        $options = array_merge($options, array_get($config, 'options', []));

        parent::__construct($dsn, $options);

        $this->setQueue($queue);
        $this->setSession($session);
    }

    /**
     * Setter for session manager
     *
     * @param  \Illuminate\Session\SessionManager|null $session
     * @return \Clowdy\Raven\Client
     */
    public function setSession($session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * Setter for queue manager
     *
     * @param  \Illuminate\Queue\QueueManager|null $queue
     * @return \Clowdy\Raven\Client
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Setter for a custom queue
     *
     * @param  string               $customQueue
     * @return \Clowdy\Raven\Client
     */
    public function setCustomQueue($customQueue)
    {
        $this->customQueue = $customQueue;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function get_user_data()
    {
        $user = [];
        if (isset($this->context) && $this->context->user) {
            $user = $this->context->user;
        }

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
        // send error now if queue not set
        if (is_null($this->queue)) {
            return $this->sendError($data);
        }

        // put the job into the queue
        // Sync connection will sent directly
        // if failed to add job to queue send it now
        try {
            $this->queue->push('Clowdy\Raven\Job', $data, $this->customQueue);
        } catch (Exception $e) {
            $this->sendError($data);
        }

        return;
    }

    /**
     * Send the error to sentry without queue
     *
     * @return void
     */
    public function sendError($data)
    {
        return parent::send($data);
    }
}
