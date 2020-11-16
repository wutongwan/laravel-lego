<?php

namespace Lego\Foundation\Concerns;

use Lego\Foundation\Events;

trait HasEvents
{
    /**
     * @var Events
     */
    protected $events;

    protected function initializeHasEvents()
    {
        $this->events = new Events();
    }

    public function getEvents()
    {
        return $this->events;
    }
}
