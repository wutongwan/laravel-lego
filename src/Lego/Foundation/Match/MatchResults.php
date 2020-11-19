<?php

namespace Lego\Foundation\Match;

use Illuminate\Support\Arr;

class MatchResults implements \JsonSerializable
{
    private $items = [];

    /**
     * @var bool
     */
    private $hasMore;

    /**
     * MatchResults constructor.
     * @param array<scalar, scalar>|array<array{id:scalar, text:scalar}> $items
     * @param false $hasMore
     */
    public function __construct(array $items = [], $hasMore = false)
    {
        if (Arr::isAssoc($items)) {
            foreach ($items as $id => $text) {
                $this->add($id, $text);
            }
        } else {
            $this->items = $items;
        }
        $this->hasMore = $hasMore;
    }

    public function add($id, $text)
    {
        $this->items[] = ['text' => $text, 'id' => $id];
    }

    /**
     * @param bool $hasMore
     */
    public function setHasMore(bool $hasMore): void
    {
        $this->hasMore = $hasMore;
    }

    public function jsonSerialize()
    {
        return [
            'items' => $this->items,
            'hasMore' => $this->hasMore,
        ];
    }
}
