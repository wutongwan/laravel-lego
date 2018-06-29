<?php

namespace Lego\Widget\Concerns;

/**
 * 提供简单的事件处理逻辑.
 *
 * Eloquent 的事件是注册到类变量中的，会影响到所有同类型 Model ，
 * 所以在这里提供了基于 Widget 的事件机制
 *
 * @property \Lego\Foundation\Event $events
 */
trait HasFormEvents
{
    public function saving(\Closure $closure)
    {
        $this->events->register('saving', null, $closure);

        return $this;
    }

    public function saved(\Closure $closure)
    {
        $this->events->register('saved', null, $closure);

        return $this;
    }

    protected function fireEvent($event)
    {
        $this->events->fire($event, [$this->data, $this]);

        return $this;
    }
}
