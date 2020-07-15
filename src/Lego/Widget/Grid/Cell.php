<?php

namespace Lego\Widget\Grid;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Lego\Foundation\Exceptions\LegoException;
use Lego\Operator\Finder;
use Lego\Operator\Store;

class Cell
{
    private $name;
    private $description;

    /**
     * @var PipeHandler[]
     */
    private $pipes = [];

    private $data;
    /**
     * @var Store
     */
    private $store;
    private $default;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array<string, string>
     */
    private $tagMapping = [];

    public function __construct($name, $description)
    {
        $pipes = explode('|', $name);
        $this->name = $pipes[0];
        $this->description = $description;

        foreach (array_slice($pipes, 1) as $pipe) {
            $this->pipe($pipe);
        }
    }

    public function name()
    {
        return $this->name;
    }

    public function description()
    {
        return $this->description;
    }

    public function default($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * 修正函数，对现有值进行一定的修正.
     *
     * $callable 可以接受两个参数
     *  - 当前值
     *  - 当前值所属的对象（ Model ）
     *
     * @param callable|string $pipe
     *
     * @return $this
     * @throws LegoException
     *
     */
    public function pipe($pipe)
    {
        $this->pipes[] = new PipeHandler($pipe, array_slice(func_get_args(), 1));

        return $this;
    }

    public function cell($callable)
    {
        return $this->pipe($callable);
    }

    public function format(string $format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @var string
     */
    private $link;
    /**
     * @var bool
     */
    private $linkOpenInNewTab;

    /**
     * Set link(<a>) to cell
     * @param string $url
     * @param bool $openInNewTab
     * @return $this
     */
    public function link(string $url, bool $openInNewTab = true)
    {
        $this->link = $url;
        $this->linkOpenInNewTab = $openInNewTab;
        return $this;
    }

    /**
     * 设置标签映射关系
     * @param array $mappings
     * @return static
     */
    public function tag(array $mappings)
    {
        $this->tagMapping = $mappings;
        return $this;
    }

    public function copy()
    {
        return clone $this;
    }

    public function fill($data): self
    {
        if ($data instanceof Store) {
            $this->data = $data->getOriginalData();
            $this->store = $data;
        } else {
            $this->data = $data;
            $this->store = Finder::createStore($data);
        }

        return $this;
    }

    public function store()
    {
        return $this->store;
    }

    /**
     * cell original value before pipes is called.
     *
     * @return string
     */
    public function getOriginalValue()
    {
        return $this->store->get($this->name);
    }

    public function getDefaultValue()
    {
        return $this->default;
    }

    /**
     * cell value after pipes processed.
     *
     * @return HtmlString
     */
    public function value()
    {
        $value = lego_default($this->getOriginalValue(), $this->default);
        foreach ($this->pipes as $pipe) {
            $value = $pipe->handle($value, $this->data, $this);
        }

        // format
        if ($this->format) {
            $value = FormatTool::format($value, $this->format, $this->store);
        }

        // tag
        if ($this->tagMapping) {
            $selected = null;
            foreach ($this->tagMapping as $pattern => $style) {
                if (Str::is($pattern, $value)) {
                    $selected = $style;
                    break;
                }
            }
            if ($selected) {
                $value = sprintf('<span class="label label-%s">%s</span>', $selected, $value);
            }
        }

        // link
        if ($this->link) {
            $value = sprintf(
                '<a href="%s" target="%s">%s</a>',
                FormatTool::format($value, $this->link, $this->store),
                $this->linkOpenInNewTab ? '_blank' : '_self',
                $value
            );
        }

        return new HtmlString((string)$value);
    }

    /**
     * cell plain value after pipes processed.
     *
     * @return string
     */
    public function getPlainValue()
    {
        return strip_tags($this->value()->toHtml());
    }

    public function __toString()
    {
        return $this->value()->toHtml();
    }
}
