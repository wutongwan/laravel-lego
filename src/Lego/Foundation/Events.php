<?php

namespace Lego\Foundation;

use Closure;

/**
 * Lego Simple Events.
 */
class Events
{
    /**
     * @var Closure[]|array
     */
    protected $events = [];


    /**
     * @var Closure[]
     */
    protected $once = [];

    /**
     * Register event.
     *
     * @param string $event     event name
     * @param string $listener  listener name
     * @param Closure $callback call when fire
     *
     * @return string|int
     */
    public function register(string $event, string $listener, Closure $callback)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        if ($listener) {
            $this->events[$event][$listener] = $callback;
        } else {
            $this->events[$event][] = $callback;
            $keys = array_keys($this->events[$event]);
            $listener = array_pop($keys);
        }

        return $listener;
    }

    /**
     * fire only once.
     * @param $event
     * @param $listener
     * @param Closure $callback
     * @return int|string
     */
    public function once(string $event, string $listener, Closure $callback)
    {
        $realListener = $this->register($event, $listener, $callback);

        $this->once[$event][$realListener] = true;

        return $realListener;
    }

    public function fire($event, $params = [])
    {
        if (!isset($this->events[$event])) {
            return;
        }

        foreach ($this->events[$event] as $listener => $callback) {
            call_user_func_array($callback, $params);

            // clear once listener
            if (isset($this->once[$event][$listener])) {
                unset($this->events[$event][$listener]);
                unset($this->once[$event][$listener]);
            }
        }
    }

    /**
     * 所有事件.
     *
     * @return Closure[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * 所有一次性事件.
     *
     * @return Closure[]
     */
    public function getOnceEvents()
    {
        return $this->once;
    }
}
