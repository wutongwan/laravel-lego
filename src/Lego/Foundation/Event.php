<?php namespace Lego\Foundation;

/**
 * Lego Simple Event.
 */
class Event
{
    /**
     * @var \Closure[]
     */
    protected static $events = [];
    protected static $once = [];

    /**
     * Register event.
     *
     * @param string $event event name
     * @param string $listener listener name
     * @param \Closure $callback call when fire
     */
    public static function register($event, $listener, \Closure $callback)
    {
        if (!isset(self::$events[$event])) {
            self::$events[$event] = [];
        }

        self::$events[$event][$listener] = $callback;
    }

    /**
     * fire only once
     */
    public static function once($event, $listener, \Closure $callback)
    {
        self::register($event, $listener, $callback);

        if (!isset(self::$once[$event])) {
            self::$once[$event] = [];
        }
        self::$once[$event][$listener] = true;
    }

    public static function fire($event)
    {
        if (!isset(self::$events[$event])) {
            return;
        }

        foreach (self::$events[$event] as $listener => $callback) {
            call_user_func($callback);

            // clear once listener
            if (isset(self::$once[$event][$listener])) {
                unset(self::$events[$event][$listener]);
                unset(self::$once[$event][$listener]);
            }
        }
    }
}
