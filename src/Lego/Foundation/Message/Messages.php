<?php

namespace Lego\Foundation\Message;

use ArrayIterator;

class Messages implements \IteratorAggregate
{
    /**
     * @var Message[]
     */
    private $items = [];

    public function info(string $message)
    {
        $this->items[] = Message::info($message);
    }

    public function error(string $message)
    {
        $this->items[] = Message::error($message);
    }

    public function errors(array $messages)
    {
        $this->items = array_merge($this->items, $messages);
    }

    /**
     * @return Message[]
     */
    public function all()
    {
        return $this->items;
    }

    public function any(): bool
    {
        return count($this->items) > 0;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
}
