<?php namespace Lego\Foundation;

class Value
{
    /**
     * 原始值, 一般为 null 或者数据库中的值
     */
    private $original;

    /**
     * 当前值, 一般为从请求数据中获取的值
     */
    private $current;

    /**
     * 展示值, 特殊需求时, 需要定义独立的展示值, 仅作展示使用, 不影响其他值
     */
    private $show;

    /**
     * 默认值, 如果以上 `original` 和 `current` 均为 null , 则返回此数值
     *
     * 如果没有定义 `show` , 默认也显示此数值
     */
    private $default;

    public function __construct($value = null)
    {
        $this->set($value);
    }

    public function set($value)
    {
        $this->original = $value;
        $this->current = $value;
    }

    public function original()
    {
        return value($this->original);
    }

    public function current()
    {
        return value($this->current);
    }

    public function show()
    {
        return value($this->show);
    }

    public function default()
    {
        return value($this->default);
    }

    public function display()
    {
        if (!is_null($show = $this->show())) {
            return $show;
        }

        if (!is_null($current = $this->current())) {
            return $current;
        }

        return $this->default();
    }

    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }

    public function setCurrent($current)
    {
        $this->current = $current;

        return $this;
    }

    public function setShow($show)
    {
        $this->show = $show;

        return $this;
    }

    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    public function __toString()
    {
        return strval($this->current());
    }
}