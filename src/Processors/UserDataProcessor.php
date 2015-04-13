<?php

namespace Clowdy\Raven\Processors;

use Auth;

class UserDataProcessor
{
    public function __invoke(array $record)
    {
        if ($user = Auth::user()) {
            $record['context']['user'] = $user->toArray();
        } else {
            $record['context']['user'] = ['id' => null];
        }
        return $record;
    }
}
