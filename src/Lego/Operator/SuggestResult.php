<?php

namespace Lego\Operator;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class SuggestResult implements Arrayable, \JsonSerializable
{
    protected $items = [];
    protected $totalCount;

    /**
     * SuggestResult constructor.
     *
     * @param array|\Illuminate\Support\Collection $items eg: [  ]
     * @param $totalCount
     */
    public function __construct($items, $totalCount = false)
    {
        if ($items instanceof Collection) {
            $items = $items->all();
        }

        $this->totalCount = $totalCount ?: count($items);

        if (0 === count($items)) {
            return;
        }

        if (Arr::isAssoc($items)) {
            // kv 数组
            foreach ($items as $value => $label) {
                $this->items[] = compact('value', 'label');
            }
        } elseif (is_scalar($items[0])) {
            // 标量数组
            $this->items = array_map(function ($item) {
                return ['value' => $item, 'label' => $item];
            }, $items);
        } else {
            // 数组数组
            $this->items = array_map(function ($item) {
                if (isset($item['id']) && isset($item['text'])) {
                    return ['value' => $item['id'], 'label' => $item['text']];
                }

                return ['value' => $item['value'], 'label' => $item['label']];
            }, $items);
        }

        $this->totalCount = $totalCount ?: count($this->items);
    }

    public function toArray()
    {
        return [
            'total_count' => $this->totalCount,
            'items'       => $this->items,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
