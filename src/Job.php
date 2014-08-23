<?php namespace Clowdy\Raven;

use App;

class Job
{
    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire($job, $data)
    {
        // Get the Raven instance.
        $raven = App::make('log.raven');

        try {
            // Send the data to Sentry.
            $raven->sendError($data);

            // Delete the processed job.
            $job->delete();
        } catch (\Exception $e) {
            // Release Job with delay.
            $job->release(30);
        }
    }

}
