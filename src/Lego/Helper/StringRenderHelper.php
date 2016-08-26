<?php namespace Lego\Helper;

use Illuminate\Support\HtmlString;
use Lego\LegoException;

/**
 * 对象转换为字符串时的工具函数, 需要宿主类实现 `public function render() : string` 函数
 *
 * Class StringRenderHelper
 * @package Lego\Helper
 */
trait StringRenderHelper
{
    /**
     * 渲染当前对象
     * @return string
     */
    abstract public function render() : string;

    /**
     * 渲染 Response view 前调用, 返回非null值时, 会作为本次请求的 Response
     */
    abstract protected function beforeRender();

    /**
     * 渲染 Response view 后调用
     */
    protected function afterRender()
    {
        // do nothing.
    }

    public function __toString()
    {
        if (method_exists($this, 'render')) {

            $string = $this->beforeRender();
            if (!is_null($string)) {
                return (string)$string;
            }

            $string = $this->render();

            $this->afterRender();

            return $string;
        }

        throw new LegoException('Undefined function `render() : string`.');
    }

    /**
     * 渲染为 HtmlString 对象, 在 view 中显示时可以直接打印, 无需关闭转义
     *
     * @return HtmlString
     */
    public function renderHtmlString()
    {
        return new HtmlString(strval($this));
    }
}