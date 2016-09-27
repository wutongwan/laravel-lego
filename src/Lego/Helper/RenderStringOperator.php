<?php namespace Lego\Helper;

use Illuminate\Support\HtmlString;

/**
 * 对象转换为字符串时的工具函数, 需要宿主类实现 `public function render() : string` 函数
 *
 * Class RenderStringOperator
 * @package Lego\Helper
 */
trait RenderStringOperator
{
    /**
     * 渲染当前对象
     * @return string
     */
    abstract public function render() : string;

    final public function __toString()
    {
        return $this->render();
    }

    /**
     * 渲染为 HtmlString 对象, 在 view 中显示时可以直接打印, 无需关闭转义
     *
     * @return HtmlString
     */
    final public function toHtmlString()
    {
        return new HtmlString($this->render());
    }
}