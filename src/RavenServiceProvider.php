<?php namespace Clowdy\Raven;

use Illuminate\Support\ServiceProvider;
use Monolog\Handler\RavenHandler;

class RavenServiceProvider extends ServiceProvider
{
    /**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
    public function boot()
    {
        if (!$this->app->config->get('raven::enabled')) {
            return;
        }

        $this->app->log = new Log($this->app->log->getMonolog());

        $this->app->log->registerHandler(
            $this->app->config->get('raven::level', 'error'),
            function ($level) {
                $handler = new RavenHandler($this->app['log.raven'], $level);

                // Add processors
                $processors = $this->app->config->get('raven::monolog.processors', []);

                if (is_array($processors)) {
                    foreach ($processors as $process) {
                        // Get callable
                        if (is_string($process)) {
                            $callable = new $process();
                        } else {
                            throw new \Exception('Raven: Invalid processor');
                        }

                        // Add processor to Raven handler
                        $handler->pushProcessor($callable);
                    }
                }

                return $handler;
            }
        );
    }

    /**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    {
        $this->app->config->package('clowdy/laravel-raven', realpath(__DIR__.'/config'), 'raven');

        $this->app->singleton('log.raven', function () {
            $config = $this->app->config->get('raven::config');

            $queue = $this->app->queue;
            $connection = $this->app->config->get('raven::queue.connection');
            if ($connection) {
                $queue->connection($connection);
            }

            $client = new Client($config, $queue);
            $client->setCustomQueue($this->app->config->get('raven::queue.queue'));

            return $client;
        });
    }

}