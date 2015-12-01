<?php

namespace Clowdy\Raven\Facades;

use Clowdy\Raven\Client;
use Illuminate\Support\Facades\Facade;

class Raven extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Client::class;
    }
}
