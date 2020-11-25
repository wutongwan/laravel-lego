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
    private $items = [];

    public function all(): array
    {
        return array_merge($this->items, [
            'class' => array_keys($this->classSet),
            'style' => $this->styleMap,
        ]);
    }

    public function set(string $name, $value): self
    {
        if ($name === 'class') {
            $this->classSet = array_flip((array)$value);
            return $this;
        }

        if ($name === 'style') {
            $value = (array)$value;
            if (Arr::isAssoc($value) === false) {
                throw new \InvalidArgumentException('`style` value should be a map/dict array');
            }
            $this->styleMap = $value;
            return $this;
        }

        $this->items[$name] = $value;
        return $this;
    }

    public function get(string $name)
    {
        if ('class' === $name) {
            return array_keys($this->classSet);
        }
        if ('style' === $name) {
            return $this->styleMap;
        }
        return $this->items[$name] ?? null;
    }

    public function has(string $name): bool
    {
        if ('class' === $name) {
            return count($this->classSet) > 0;
        }
        if ('style' === $name) {
            return count($this->styleMap) > 0;
        }
        return array_key_exists($name, $this->items);
    }

    public function addClass(string $class): self
    {
        if (str_contains($class, ' ')) {
            foreach (explode(' ', $class) as $item) {
                $this->classSet[$item] = true;
            }
        } else {
            $this->classSet[$class] = true;
        }

        return $this;
    }

    public function removeClass(string $class): self
    {
        unset($this->classSet[$class]);
        return $this;
    }

    public function setStyle(string $name, string $value): self
    {
        $this->styleMap[$name] = $value;
        return $this;
    }

    public function removeStyle(string $name): self
    {
        unset($this->styleMap[$name]);
        return $this;
    }

    public function merge(self $attributes)
    {
        $this->classSet += $attributes->classSet;
        $this->styleMap += $attributes->styleMap;
        $this->items = array_merge($this->items, $attributes->items);
    }

    public function getClassString(): string
    {
        return join(' ', array_keys($this->classSet));
    }

    public function toString(array $ignore = []): string
    {
        $parts = [];

        // class
        in_array('class', $ignore) || $parts[] = "class=\"{$this->getClassString()}\"";

        // style
        if ($this->styleMap && !in_array('style', $ignore)) {
            $style = 'style="';
            foreach ($this->styleMap as $name => $value) {
                $style .= "{$name}: {$value};";
            }
            $style .= '"';
            $parts[] = $style;
        }

        // other attributes
        foreach ($this->items as $key => $value) {
            if (in_array($key, $ignore)) {
                continue;
            }
            if ($value === null) {
                $parts[] = $key;
                continue;
            }
            // Treat boolean attributes as HTML properties
            if (is_bool($value) && $key !== 'value') {
                $parts[] = $value ? $key : '';
                continue;
            }
            $parts[] = $key . '="' . e($value) . '"';
        }

        return join(' ', $parts);
    }

    public function __toString()
    {
        return $this->toString();
    }
}
