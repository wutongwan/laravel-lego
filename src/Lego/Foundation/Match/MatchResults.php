<?php

namespace Lego\Foundation\Match;

use Illuminate\Support\Arr;

class MatchResults implements \JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    private $items = [];

    private $totalCount = 0;

    public function __construct(array $items = [])
    {
        if (Arr::isAssoc($items)) {
            foreach ($items as $value => $label) {
                $this->add($value, $label);
            }
        } else {
            $this->items = $items;
            $this->totalCount = count($items);
        }
    }

    public function add($value, $label)
    {
        $this->items[] = [
            'label' => $label,
            'value' => $value,
        ];
        $this->totalCount++;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    public function all()
    {
        $options = [];
        foreach ($this->options as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }
        return $options;
    }

    public function jsonSerialize()
    {
        return [
            'items' => $this->items,
            'totalCount' => $this->totalCount,
        ];
    }
}
