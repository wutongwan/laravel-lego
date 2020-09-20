<?php

namespace Lego\Foundation\Html;

use Illuminate\Support\Arr;

class HtmlAttributes
{
    /**
     * class 集合，使用 array key 存放 class 列表实现 set 的效果
     * @var array
     */
    private $classSet = [];

    /**
     * @var string[]|array<string, string>
     */
    private $styleMap = [];

    /**
     * @var string[]|array<string, string>
     */
    private $attributes = [];

    public function getAttributes(): array
    {
        return array_merge($this->attributes, [
            'class' => array_keys($this->classSet),
            'style' => $this->styleMap,
        ]);
    }

    public function setAttribute(string $name, $value): void
    {
        if ($name === 'class') {
            $this->classSet = array_flip((array)$value);
            return;
        }

        if ($name === 'style') {
            $value = (array)$value;
            if (Arr::isAssoc($value) === false) {
                throw new \InvalidArgumentException('`style` value should be a map/dict array');
            }
            $this->styleMap = $value;
            return;
        }

        $this->attributes[$name] = $value;
    }

    public function getAttribute(string $name)
    {
        if ('class' === $name) {
            return array_keys($this->classSet);
        }
        if ('style' === $name) {
            return $this->styleMap;
        }
        return $this->attributes[$name] ?? null;
    }

    public function addClass(string $class): void
    {
        $this->classSet[$class] = true;
    }

    public function removeClass(string $class): void
    {
        unset($this->classSet[$class]);
    }

    public function setStyle(string $name, string $value)
    {
        $this->styleMap[$name] = $value;
    }

    public function removeStyle(string $name)
    {
        unset($this->styleMap[$name]);
    }
}
