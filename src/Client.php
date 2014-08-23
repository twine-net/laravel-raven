<?php namespace Clowdy\Raven;

use App, Session;
use Raven_Client;
use Illuminate\Queue\QueueManager;

class Client extends Raven_Client {

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    protected $customQueue;

    /**
     * Constructor.
     */
    public function __construct($config = [], QueueManager $queue)
    {
        $dsn = array_get($config, 'dsn', '');
        $options = array_get($config, 'options', []);

        parent::__construct($dsn, $options);

        $this->queue = $queue;
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
        $user = $this->context->user ?: array();
        $session = Session::all();

        // Add Laravel session data
        if (isset($user['data']))
        {
            $user['data'] = array_merge($session, $user['data']);
        }
        else
        {
            $user['data'] = $session;
        }

        // Add session ID
        if ( ! isset($user['id']))
        {
            $user['id'] = Session::getId();
        }

        return array(
            'sentry.interfaces.User' => $user,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get_default_data()
    {
        // Add additional tags
        $this->tags['environment'] = App::environment();

        return parent::get_default_data();
    }

    /**
     * {@inheritdoc}
     */
    public function send($data)
    {
        // put the job into the queue
        // Sync connection will sent directly
        $this->queue->push('Clowdy\Raven\Job', $data, $this->customQueue);
    }

    public function sendError($data)
    {
        return parent::send($data);
    }

}