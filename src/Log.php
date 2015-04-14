<?php

namespace Clowdy\Raven;

use Closure;
use Exception;
use Illuminate\Log\Writer;

/**
 * Overrides default Logger to provide extra functionality.
 */
class Log extends Writer
{
    /**
     * Write a message to Monolog.
     *
     * @param  string $level
     * @param  string $message
     * @param  array  $context
     * @return void
     */
    protected function writeLog($level, $message, $context)
    {
        if (is_a($message, 'Exception')) {
            // Set context exception using exception
            $context = array_merge($context, ['exception' => $message]);

            $message = $message->getMessage();
        }
        
        $context = array_merge(['logger' => 'laravel-raven'], $context);

        parent::writeLog($level, $message, $context);
    }

    /**
     * Register a new Monolog handler.
     *
     * @param string   $level   Laravel log level.
     * @param \Closure $closure Return an instance of \Monolog\Handler\HandlerInterface.
     *
     * @throws \InvalidArgumentException Unknown log level.
     *
     * @return bool Whether handler was registered.
     */
    public function registerHandler($level, Closure $callback)
    {
        $level   = $this->parseLevel($level);
        $handler = call_user_func($callback, $level);

        // Add handler to Monolog
        $this->getMonolog()->pushHandler($handler);

        return true;
    }
}
