<?php

namespace Clowdy\Raven\Processors;

use Illuminate\Support\Facades\App;

/**
 * This processor adds the current locale to an outgoing error report as a tag.
 */
class LocaleProcessor
{
    /**
     * Run the processor: attach locale tag to a Monolog record
     *
     * @param array $record Monolog record
     * @return array $record
     */
    public function __invoke(array $record)
    {
        $record['extra']['tags']['locale'] = $this->getLocale();
        return $record;
    }

    /**
     * Get the locale
     *
     * @return string
     */
    public function getLocale()
    {
        return App::getLocale();
    }
}
