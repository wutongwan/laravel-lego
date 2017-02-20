<?php namespace Lego\Widget\Concerns;

use Illuminate\Support\Arr;

/**
 * 提供简单的事件处理逻辑
 *
 * Eloquent 的事件是注册到类变量中的，会影响到所有同类型 Model ，
 * 所以在这里提供了基于 Widget 的事件机制
 */
trait HasEvents
{
    protected $events = [];

    public function saving(\Closure $closure)
    {
        return $this->addEvent('saving', $closure);
    }

    public function saved(\Closure $closure)
    {
        return $this->addEvent('saved', $closure);
    }

    private function addEvent($event, \Closure $closure)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event] = $closure;

        return $this;
    }

    protected function fireEvent($event)
    {
        foreach (Arr::get($this->events, $event, []) as $closure) {
            call_user_func_array($closure, [$this->data, $this]);
        }
    }
}
