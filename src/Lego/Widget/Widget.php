<?php namespace Lego\Widget;

abstract class Widget
{
    public static function source($source = [])
    {
        $widget = new static($source);
        $widget->validateSource($source); // 保证数据源符合约定
        return $widget;
    }

    private $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    protected function getSource()
    {
        return $this->source;
    }

    /**
     * 验证 source 是否符合约定
     */
    abstract protected function validateSource($source);
}