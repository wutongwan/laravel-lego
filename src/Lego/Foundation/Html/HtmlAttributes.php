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

    public function setAttribute(string $name, $value): self
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

    public function getAttribute(string $name)
    {
        if ('class' === $name) {
            return array_keys($this->classSet);
        }
        if ('style' === $name) {
            return $this->styleMap;
        }
        return $this->items[$name] ?? null;
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

    public function __toString()
    {
        $parts = [];
        foreach ($this->items as $key => $value) {
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

        $parts[] = 'class="' . join(' ', array_keys($this->classSet)) . '"';

        if ($this->styleMap) {
            $style = 'style="';
            foreach ($this->styleMap as $name => $value) {
                $style .= "{$name}: {$value};";
            }
            $style .= '"';
            $parts[] = $style;
        }

        return join(' ', $parts);
    }
}
