<?php namespace Lego\Foundation\Concerns;

use Lego\Foundation\Event;

trait HasEvents
{
    /**
     * @var Event
     */
    protected $events;

    protected function initializeHasEvents()
    {
        $this->events = new Event();
    }

    public function getEvents()
    {
        return $this->events;
    }
}
