<?php

namespace Clowdy\Raven;

class Job
{
    /**
     * @var \Clowdy\Raven\Client
     */
    protected $raven;

    /**
     * @param \Clowdy\Raven\Client $raven
     */
    public function __construct(Client $raven)
    {
        $this->raven = $raven;
    }

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire($job, $data)
    {
        try {
            // Send the data to Sentry.
            $this->raven->sendError($data);

            // Delete the processed job.
            $job->delete();
        } catch (\Exception $e) {
            // Release Job with delay.
            $job->release(30);
        }
    }
}
